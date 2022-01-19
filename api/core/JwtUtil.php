<?php

namespace PHPSeed\Core;

use PHPSeed\Core\DI;
use DateTimeZone;
use DateTimeImmutable;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Exception;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\IdentifiedBy;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;

class JwtUtil
{
    use Singleton;

    private $config;
    private $jwtConfig;

    private function __construct()
    {
        $this->config = DI::getInstance()->config->jwt;
        $this->jwtConfig = Configuration::forAsymmetricSigner(
            // You may use RSA or ECDSA and all their variations (256, 384, and 512) and EdDSA over Curve25519
            new Signer\Rsa\Sha256(),
            InMemory::file($this->config->signingKey),
            InMemory::base64Encoded($this->config->verificationKey)
            // You may also override the JOSE encoder/decoder if needed by providing extra arguments here
        );
    }

    /**
     * 生成 token
     */
    public function getToken($memberId, array $payload = [])
    {
        $now = new DateTimeImmutable();

        $jwtObj = $this->jwtConfig->builder()
            ->issuedBy($this->config->issuedBy)
            ->permittedFor($this->config->permittedFor)
            ->identifiedBy($this->config->identifiedBy)
            ->issuedAt($now)
            // 在1分钟后才可使用
            // ->canOnlyBeUsedAfter($now->modify("+1 minute"))
            ->expiresAt($now->modify("+" . $this->config->expiresMinutes . " minute"));

        // 装载 payload
        // "role": "ADMIN"
        // "auth": "article:add,article:delete"
        if (is_array($payload) && !empty($payload)) {
            foreach ($payload as $key => $value) {
                $jwtObj->withClaim($key, $value);
            }
        }

        // 生成 token
        $token = $jwtObj
            ->getToken($this->jwtConfig->signer(), $this->jwtConfig->signingKey())
            ->toString();

        $this->save2Redis($memberId, $token);

        return $token;
    }

    /**
     * 保存 token 到 redis
     */
    public function save2Redis($memberId, $token)
    {
        // 保存 token（1有效，0有效但已登出）
        //      因为账户登出后 JWT 本身只要没过期就仍然有效，所以只能通过 redis 缓存来校验有无效
        //      校验时只要 redis 中的 token 无效即可（JWT 本身可以校验有无过期，而 redis 过期即被删除了）
        RedisUtil::getInstance()->setex($token, $this->config->expiresMinutes * 60, 1);
        // 用户无需重复请求 token
        RedisUtil::getInstance()->setex($memberId, $this->config->expiresMinutes * 60, $token);
    }

    /**
     * 验证 token & redis
     */
    public function validateTokenAndRedis($token)
    {
        $redisTokenValidate = RedisUtil::getInstance()->get($token) == 1;
        return $this->validateToken($token) && $redisTokenValidate;
    }

    /**
     * 验证 token
     */
    public function validateToken($token)
    {
        try {
            $token = $this->jwtConfig->parser()->parse($token);
        } catch (Exception $e) {
            // var_dump($e);
            return false;
        }

        $this->jwtConfig->setValidationConstraints(new IdentifiedBy($this->config->identifiedBy));
        $this->jwtConfig->setValidationConstraints(new IssuedBy($this->config->issuedBy));
        $this->jwtConfig->setValidationConstraints(new PermittedFor($this->config->permittedFor));
        $time = new SystemClock(new DateTimeZone('Asia/Shanghai'));
        // 包的问题，能读取，下同
        $this->jwtConfig->setValidationConstraints(new ValidAt($time));

        $validationConstraints  = $this->jwtConfig->validationConstraints();
        try {
            $this->jwtConfig->validator()->assert($token, ...$validationConstraints);
            return true;
        } catch (RequiredConstraintsViolated $e) {
            // var_dump($e);
            return false;
        }
    }

    /**
     * 解析 token
     */
    public function parseToken($token)
    {
        try {
            $token = $this->jwtConfig->parser()->parse($token);
            // 包的问题，能读取
            $claims = json_decode(base64_decode($token->claims()->toString()), true);
        } catch (Exception $e) {
            // var_dump($e);
            return false;
        }
        return $claims;
    }
}
