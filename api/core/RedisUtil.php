<?php

namespace Seed\Core;

use Seed\Core\Singleton;
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
        $this->redis = new Client([
            "scheme" => "tcp",
            "host" => "127.0.0.1",
            "port" => 6379,
            "cache" => 0,
            "password" => "root",

        ]);
    }
}
