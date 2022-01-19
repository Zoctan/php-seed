<?php

namespace PHPSeed\Core;

use PHPSeed\Core\DI;
use Predis\Client;

/**
 * expireResolution 过期策略
 *       EX seconds -- Set the specified expire time, in seconds. 秒
 *       PX milliseconds -- Set the specified expire time, in milliseconds. 毫秒
 *       NX -- Only set the key if it does not already exist. 不存在则设置
 *       XX -- Only set the key if it already exist. 存在则设置
 * expireTTL 过期时间
 */
class RedisUtil
{
    use Singleton;

    private $redis = null;

    private function __construct()
    {
        $config = DI::getInstance()->config->datasource->redis;
        $this->redis = new Client([
            "scheme" => $config["scheme"],
            "host" => $config["host"],
            "port" => $config["port"],
            "cache" => $config["cache"],
            "password" => $config["password"],
        ]);
    }
}
