<?php

namespace App\Util;

use Predis\Client;
use App\Model\MemberModel;
use App\Core\AuthMember;
use App\Core\Singleton;
use App\Core\exception\UnAuthorizedException;
use App\Core\Http\Request;
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

    /**
     * @var object
     */
    private $config;

    /**
     * @var Client
     */
    private $cache;

    /**
     * @var Configuration
     */
    private $jwtConfig;

    private function __construct()
    {
        $this->config = \App\DI()->config->jwt;
        $this->cache = \App\DI()->cache;

        // https://lcobucci-jwt.readthedocs.io/en/latest/configuration/
        $this->jwtConfig = Configuration::forAsymmetricSigner(
            // You may use RSA or ECDSA and all their variations (256, 384, and 512) and EdDSA over Curve25519
            new Signer\Rsa\Sha256(),
            InMemory::file($this->config->signingKey),
            InMemory::base64Encoded($this->config->verificationKey)
            // You may also override the JOSE encoder/decoder if needed by providing extra arguments here
        );
    }

    /**
     * 从请求头或请求参数中获取 token
     */
    public function getTokenFromRequest(Request $request)
    {
        $headerKey = $this->config->header;
        $token = $request->headers->get($headerKey);
        return !empty($token) ? $token : $request->get($headerKey);
    }

    /**
     * 签发 token
     */
    public function sign($memberId, array $payload = [])
    {
        // $token = $this->cache->get($memberId);
        // if ($token) {
        //     $ttl = $this->cache->ttl($memberId);
        //     // 如果 redis 存在，并且有超过15分钟的有效期，就不签发新 token
        //     if ($ttl > $this->config->refreshMinutes * 60) {
        //         return $token;
        //     } else if (0 < $ttl &&  $ttl <= $this->config->refreshMinutes * 60) {
        //         // 最后15分钟有效期，使原先的 token 无效，重新签发
        //         $this->invalidRedisToken($memberId);
        //     }
        // }

        $now = new \DateTimeImmutable();

        $jwtObj = $this->jwtConfig->builder()
            ->issuedBy($this->config->issuedBy)
            ->permittedFor($this->config->permittedFor)
            ->identifiedBy($this->config->identifiedBy)
            ->issuedAt($now)
            // 在1分钟后才可使用
            // ->canOnlyBeUsedAfter($now->modify("+1 minute"))
            ->expiresAt($now->modify("+" . $this->config->expiresMinutes . " minute"));

        // 装载 payload
        // "memberId": 1
        // "role": "ADMIN"
        // "operate": "article:add,article:delete"
        $jwtObj->withClaim("memberId", $memberId);
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
        $this->cache->setex($token, $this->config->expiresMinutes * 60, 1);
        // 用户无需重复请求 token
        $this->cache->setex($memberId, $this->config->expiresMinutes * 60, $token);
    }

    /**
     * 使 redis 里的 token失效
     */
    public function invalidRedisToken($memberId)
    {
        $token = $this->cache->get($memberId);
        $this->cache->setex($token, $this->config->expiresMinutes * 60, 0);
    }

    /**
     * 验证 token & redis
     */
    public function validateTokenRedis($token)
    {
        $redisToken = $this->cache->get($token);
        if ($redisToken && intval($redisToken) == 1) {
            return $this->validateToken($token);
        }
        return false;
    }

    /**
     * 验证 token
     */
    public function validateToken($token)
    {
        try {
            $token = $this->jwtConfig->parser()->parse($token);
        } catch (Exception $e) {
            throw new UnAuthorizedException("token 解析错误：" . $e->getMessage());
        }

        $this->jwtConfig->setValidationConstraints(new IdentifiedBy($this->config->identifiedBy));
        $this->jwtConfig->setValidationConstraints(new IssuedBy($this->config->issuedBy));
        $this->jwtConfig->setValidationConstraints(new PermittedFor($this->config->permittedFor));
        $time = new SystemClock(new \DateTimeZone('Asia/Shanghai'));
        $this->jwtConfig->setValidationConstraints(new ValidAt($time));

        $validationConstraints  = $this->jwtConfig->validationConstraints();
        try {
            $this->jwtConfig->validator()->assert($token, ...$validationConstraints);
            return true;
        } catch (RequiredConstraintsViolated $e) {
            throw new UnAuthorizedException("token 解析错误：" . $e->getMessage());
        }
    }

    public function getAuthentication($token)
    {
        $claims =  $this->parseToken($token);
        $role = $claims[$this->config->tokenRoleKey];
        $operate = $claims[$this->config->tokenOperateKey];
        $memberId = $claims["memberId"];
        $member = (new MemberModel())->getById(["id", "username", "status"], $memberId);
        return new AuthMember($member, $role, $operate);
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
            throw new UnAuthorizedException("token 解析错误：" . $e->getMessage());
        }
        return $claims;
    }
}
