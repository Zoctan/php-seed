<?php

namespace App\Util;

use Predis\Client;
use App\Model\AuthMemberModel;
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
        $this->config = \App\DI()->config['jwt'];
        $this->cache = \App\DI()->cache;

        // https://lcobucci-jwt.readthedocs.io/en/latest/configuration/
        $this->jwtConfig = Configuration::forAsymmetricSigner(
            // You may use RSA or ECDSA and all their variations (256, 384, and 512) and EdDSA over Curve25519
            new Signer\Rsa\Sha256(),
            InMemory::file($this->config['signingKey']),
            InMemory::base64Encoded($this->config['verificationKey'])
            // You may also override the JOSE encoder/decoder if needed by providing extra arguments here
        );
    }

    /**
     * 从请求头或请求参数中获取 token
     */
    public function getTokenFromRequest(Request $request)
    {
        $headerKey = $this->config['header'];
        $token = $request->header->get($headerKey);
        return !empty($token) ? $token : $request->get($headerKey);
    }

    /**
     * 签发 accessToken 和 refreshToken
     */
    public function sign($memberId, array $payload = [])
    {
        $accessToken  = $this->signAccessToken($memberId,  $payload);
        $refreshToken = $this->signRefreshToken($memberId,  $payload);

        return [
            'accessToken' => $accessToken,
            'refreshToken' => $refreshToken,
        ];
    }

    /**
     * 签发 accessToken
     */
    public function signAccessToken($memberId, array $payload = [])
    {
        $accessToken = $this->cache->get($memberId);
        if (empty($accessToken)) {
            $accessToken = $this->signToken($memberId, $this->config['expiresMinutes'], $payload);
            $this->save2Redis($memberId, $accessToken);
        }
        return $accessToken;
    }

    /**
     * 签发 refreshToken
     */
    public function signRefreshToken($memberId, array $payload = [])
    {
        return $this->signToken($memberId, $this->config['refreshMinutes'], $payload);
    }

    /**
     * 签发 token
     */
    public function signToken($memberId, $expiresMinutes, array $payload = [])
    {
        $now = new \DateTimeImmutable();

        $jwtObj = $this->jwtConfig->builder()
            ->issuedBy($this->config['issuedBy'])
            ->permittedFor($this->config['permittedFor'])
            ->identifiedBy($this->config['identifiedBy'])
            ->issuedAt($now)
            // 在1分钟后才可使用
            // ->canOnlyBeUsedAfter($now->modify('+1 minute'))
            ->expiresAt($now->modify('+' . $expiresMinutes . ' minute'));

        // 装载 payload
        $jwtObj->withClaim('memberId', $memberId);
        // 不适合放操作权限列表，token 后期可能会非常长
        // 这里可能会有个疑惑，放不了什么东西，那为什么不用 UUID 这些做 token
        // 因为 JWT 有私钥签名，安全性高，如果只是用作资源授权（到时即过期），这个 JWT 还是很好的
        if (is_array($payload) && !empty($payload)) {
            foreach ($payload as $key => $value) {
                $jwtObj->withClaim($key, $value);
            }
        }

        // 生成 token
        $token = $jwtObj
            ->getToken($this->jwtConfig->signer(), $this->jwtConfig->signingKey())
            ->toString();

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
        $this->cache->setex($token, $this->config['expiresMinutes'] * 60, 1);
        // 成员无需重复请求签发 token
        $this->cache->setex($memberId, $this->config['expiresMinutes'] * 60, $token);
    }

    /**
     * 使 redis 里的 token 失效
     */
    public function invalidRedisToken($memberId)
    {
        // 直接删除
        $token = $this->cache->get($memberId);
        $this->cache->del([$token, $memberId]);
        // $token = $this->cache->get($memberId);
        // $this->cache->setex($token, $this->config['expiresMinutes'] * 60, 0);
    }

    /**
     * 验证 token & redis
     */
    public function validateTokenRedis($token)
    {
        $tokenValid = $this->validateToken($token);
        if ($tokenValid) {
            $redisToken = $this->cache->get($token);
            return $redisToken && intval($redisToken) == 1;
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
            // throw new UnAuthorizedException('token parse error: ' . $e->getMessage());
            return false;
        }

        $this->jwtConfig->setValidationConstraints(new IdentifiedBy($this->config['identifiedBy']));
        $this->jwtConfig->setValidationConstraints(new IssuedBy($this->config['issuedBy']));
        $this->jwtConfig->setValidationConstraints(new PermittedFor($this->config['permittedFor']));
        $time = new SystemClock(new \DateTimeZone('Asia/Shanghai'));
        $this->jwtConfig->setValidationConstraints(new ValidAt($time));

        $validationConstraints  = $this->jwtConfig->validationConstraints();
        try {
            $this->jwtConfig->validator()->assert($token, ...$validationConstraints);
            return true;
        } catch (RequiredConstraintsViolated $e) {
            // throw new UnAuthorizedException('token validate error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 获取认证用户
     */
    public function getAuthMember($token)
    {
        $claims =  $this->parseToken($token);
        $memberId = $claims['memberId'];
        return (new AuthMemberModel())->get($memberId);
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
            throw new UnAuthorizedException('token parse error: ' . $e->getMessage());
        }
        return $claims;
    }
}
