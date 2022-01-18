<?php

namespace PHPSeed\Core;

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

    // 签发人
    private $issuedBy = "seed";
    // 受众
    private $permittedFor = "member";
    // 签发人
    private $identifiedBy = "123";
    // 多久过期（分钟）
    private $expiresMinutes = "1";
    // 私钥
    private $signingKey = __DIR__ . "/../rsa/private-key.pem";
    // 公钥
    private $verificationKey = "MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAKw+D9cjGEbEuGEhGwe1dy0LP/ujK02wHZ5RfAnWp4Hg/PYEa6fbM/DLrSNbNsTj56Wr0r/B3gd1acBNSMNVitkCAwEAAQ==";

    private function __construct()
    {
    }
    
    private function createJwtConfig()
    {
        return Configuration::forAsymmetricSigner(
            // You may use RSA or ECDSA and all their variations (256, 384, and 512) and EdDSA over Curve25519
            new Signer\Rsa\Sha256(),
            InMemory::file($this->signingKey),
            InMemory::base64Encoded($this->verificationKey)
            // You may also override the JOSE encoder/decoder if needed by providing extra arguments here
        );
    }

    /**
     * 生成 token
     */
    public function getToken($memberId, array $payload = [])
    {
        $now = new DateTimeImmutable();

        $jwtConfig = $this->createJwtConfig();
        $jwtObj = $jwtConfig->builder()
            ->issuedBy($this->issuedBy)
            ->permittedFor($this->permittedFor)
            ->identifiedBy($this->identifiedBy)
            ->issuedAt($now)
            // 在1分钟后才可使用
            // ->canOnlyBeUsedAfter($now->modify("+1 minute"))
            ->expiresAt($now->modify("+$this->expiresMinutes minute"));

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
            ->getToken($jwtConfig->signer(), $jwtConfig->signingKey())
            ->toString();

        // 保存 token（1有效，0有效但已登出）
        //      因为账户登出后 JWT 本身只要没过期就仍然有效，所以只能通过 redis 缓存来校验有无效
        //      校验时只要 redis 中的 token 无效即可（JWT 本身可以校验有无过期，而 redis 过期即被删除了）
        RedisUtil::getInstance()->setex($token, $this->expiresMinutes * 60, 1);
        // 用户无需重复请求 token
        RedisUtil::getInstance()->setex($memberId, $this->expiresMinutes * 60, $token);

        return $token;
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
        $jwtConfig = $this->createJwtConfig();
        try {
            $token = $jwtConfig->parser()->parse($token);
        } catch (Exception $e) {
            // var_dump($e);
            return false;
        }

        $jwtConfig->setValidationConstraints(new IdentifiedBy($this->identifiedBy));
        $jwtConfig->setValidationConstraints(new IssuedBy($this->issuedBy));
        $jwtConfig->setValidationConstraints(new PermittedFor($this->permittedFor));
        $time = new SystemClock(new DateTimeZone('Asia/Shanghai'));
        // 包的问题，能读取，下同
        $jwtConfig->setValidationConstraints(new ValidAt($time));

        $validationConstraints  = $jwtConfig->validationConstraints();
        try {
            $jwtConfig->validator()->assert($token, ...$validationConstraints);
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
        $jwtConfig = $this->createJwtConfig();
        try {
            $token = $jwtConfig->parser()->parse($token);
            // 包的问题，能读取
            $claims = json_decode(base64_decode($token->claims()->toString()), true);
        } catch (Exception $e) {
            // var_dump($e);
            return false;
        }
        return $claims;
    }
}
