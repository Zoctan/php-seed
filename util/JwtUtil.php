<?php

namespace App\Util;

use Predis\Client;
use App\Model\AuthMemberModel;
use App\Core\Singleton;
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

    /**
     * date time zone
     * @var string
     */
    private $dateTimeZone = 'Asia/Shanghai';

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
     * get token from request header or data
     * 
     * @param $request
     */
    public function getTokenFromRequest()
    {
        $request = \App\DI()->request;
        $headerKey = $this->config['header'];
        $token = $request->header->get($headerKey);
        return !empty($token) ? $token : $request->get($headerKey);
    }

    /**
     * sign access token and refresh token
     * 
     * @param $memberId
     * @param $payload
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
     * sign access token
     * 
     * @param $memberId
     * @param $payload
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
     * sign refresh token
     * 
     * @param $memberId
     * @param $payload
     */
    public function signRefreshToken($memberId, array $payload = [])
    {
        return $this->signToken($memberId, $this->config['refreshMinutes'], $payload);
    }

    /**
     * sign token
     * 
     * @param $memberId
     * @param $expiresMinutes
     * @param $payload
     */
    public function signToken($memberId, $expiresMinutes, array $payload = [])
    {
        $now = new \DateTimeImmutable();

        $jwtObj = $this->jwtConfig->builder()
            ->issuedBy($this->config['issuedBy'])
            ->permittedFor($this->config['permittedFor'])
            ->identifiedBy($this->config['identifiedBy'])
            ->issuedAt($now)
            // can only be used after 1 minute
            // ->canOnlyBeUsedAfter($now->modify('+1 minute'))
            ->expiresAt($now->modify('+' . $expiresMinutes . ' minute'));

        // put payload
        $jwtObj->withClaim('memberId', $memberId);
        // 不适合放操作权限列表，token 后期可能会非常长
        // 这里可能会有个疑惑，放不了什么东西，那为什么不用 UUID 这些做 token
        // 因为 JWT 有私钥签名，安全性高，如果只是用作资源授权（到时即过期），这个 JWT 还是很好的
        if (is_array($payload) && !empty($payload)) {
            foreach ($payload as $key => $value) {
                $jwtObj->withClaim($key, $value);
            }
        }

        // create token
        $token = $jwtObj
            ->getToken($this->jwtConfig->signer(), $this->jwtConfig->signingKey())
            ->toString();

        return $token;
    }

    /**
     * save token to redis
     * 
     * @param $memberId
     * @param $token
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
     * invalid redis token
     * 
     * @param $memberId
     */
    public function invalidRedisToken($memberId)
    {
        // remove redis token
        $token = $this->cache->get($memberId);
        $this->cache->del([$token, $memberId]);
    }

    /**
     * validate token self and redis token
     * 
     * @param string $token
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
     * validate token
     * 
     * @param string $token
     */
    public function validateToken($token)
    {
        try {
            $token = $this->jwtConfig->parser()->parse($token);
        } catch (Exception $e) {
            return false;
        }

        $this->jwtConfig->setValidationConstraints(new IdentifiedBy($this->config['identifiedBy']));
        $this->jwtConfig->setValidationConstraints(new IssuedBy($this->config['issuedBy']));
        $this->jwtConfig->setValidationConstraints(new PermittedFor($this->config['permittedFor']));
        $time = new SystemClock(new \DateTimeZone($this->dateTimeZone));
        $this->jwtConfig->setValidationConstraints(new ValidAt($time));

        $validationConstraints  = $this->jwtConfig->validationConstraints();
        try {
            $this->jwtConfig->validator()->assert($token, ...$validationConstraints);
            return true;
        } catch (RequiredConstraintsViolated $e) {
            return false;
        }
    }

    /**
     * get authentication member
     * 
     * @param string $token
     */
    public function getAuthMember($token)
    {
        $claims =  $this->parseToken($token);
        if (empty($claims)) {
            return null;
        }
        $memberId = $claims['memberId'];
        return (new AuthMemberModel())->get($memberId);
    }

    /**
     * parse token
     * 
     * @param string $token
     */
    public function parseToken($token)
    {
        try {
            $token = $this->jwtConfig->parser()->parse($token);
            $claims = json_decode(base64_decode($token->claims()->toString()), true);
        } catch (Exception $e) {
            return null;
        }
        return $claims;
    }
}
