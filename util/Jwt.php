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

class Jwt
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
     * Date time zone
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
     * Get token from request header or data
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
     * Sign access token and refresh token
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
     * Sign access token
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
     * Sign refresh token
     * 
     * @param $memberId
     * @param $payload
     */
    public function signRefreshToken($memberId, array $payload = [])
    {
        return $this->signToken($memberId, $this->config['refreshMinutes'], $payload);
    }

    /**
     * Sign token
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
        // jwt is very helpful when authenticate resource for third party using
        // but do not put so much data in payload
        // and token will be too long to transfer
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
     * Save token to redis
     * 
     * @param $memberId
     * @param $token
     */
    public function save2Redis($memberId, $token)
    {
        // save token itself (0:invalid | 1:valid)
        $this->cache->setex($token, $this->config['expiresMinutes'] * 60, 1);
        // save token in member id
        // if member login again, give the redis token, not sign again
        $this->cache->setex($memberId, $this->config['expiresMinutes'] * 60, $token);
    }

    /**
     * Invalid redis token
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
     * Validate token self and redis token
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
     * Validate token
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
     * Get authentication member
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
        return (new AuthMemberModel())->getByMemberId($memberId);
    }

    /**
     * Parse token
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
