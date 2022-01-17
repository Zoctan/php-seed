<?php
require_once "../vendor/autoload.php";

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
    // 签发人
    private static $issuedBy = "php-admin";
    // 受众
    private static $permittedFor = "member";
    // 签发人
    private static $identifiedBy = "123";
    // 多久过期
    private static $expiresAt = "+1 minute";
    // 私钥
    private static $signingKey = __DIR__ . "/../rsa/private-key.pem";
    // 公钥
    private static $verificationKey = "MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAKw+D9cjGEbEuGEhGwe1dy0LP/ujK02wHZ5RfAnWp4Hg/PYEa6fbM/DLrSNbNsTj56Wr0r/B3gd1acBNSMNVitkCAwEAAQ==";

    private static function createJwtConfig()
    {
        return Configuration::forAsymmetricSigner(
            // You may use RSA or ECDSA and all their variations (256, 384, and 512) and EdDSA over Curve25519
            new Signer\Rsa\Sha256(),
            InMemory::file(self::$signingKey),
            InMemory::base64Encoded(self::$verificationKey)
            // You may also override the JOSE encoder/decoder if needed by providing extra arguments here
        );
    }

    /**
     * 生成 token
     */
    public static function getToken(array $payload = [])
    {
        $now = new DateTimeImmutable();

        $jwtConfig = self::createJwtConfig();
        $jwtObj = $jwtConfig->builder()
            ->issuedBy(self::$issuedBy)
            ->permittedFor(self::$permittedFor)
            ->identifiedBy(self::$identifiedBy)
            ->issuedAt($now)
            // 在1分钟后才可使用
            // ->canOnlyBeUsedAfter($now->modify("+1 minute"))
            ->expiresAt($now->modify(self::$expiresAt));
        if (is_array($payload) && !empty($payload)) {
            foreach ($payload as $key => $value) {
                $jwtObj->withClaim($key, $value);
            }
        }
        $token = $jwtObj
            ->getToken($jwtConfig->signer(), $jwtConfig->signingKey())
            ->toString();

        // 保存 token
        // 因为账户登出后 JWT 本身只要没过期就仍然有效，所以只能通过 redis 缓存来校验有无效
        // 校验时只要 redis 中的 token 无效即可（JWT 本身可以校验有无过期，而 redis 过期即被删除了）
        redisUtils.setValue($token, true, expireTime);
        return $token;
    }


    /**
     * 验证 token
     */
    public static function validateToken($token)
    {
        $jwtConfig = self::createJwtConfig();
        try {
            $token = $jwtConfig->parser()->parse($token);
        } catch (Exception $e) {
            // var_dump($e);
            return false;
        }

        $jwtConfig->setValidationConstraints(new IdentifiedBy(self::$identifiedBy));
        $jwtConfig->setValidationConstraints(new IssuedBy(self::$issuedBy));
        $jwtConfig->setValidationConstraints(new PermittedFor(self::$permittedFor));
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
    public static function parseToken($token)
    {
        $jwtConfig = self::createJwtConfig();
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
