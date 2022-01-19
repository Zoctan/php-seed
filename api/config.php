<?php

$config = [
    "app" => [
        "name" => "PHPSeed",
        "description" => "PHP 种子",
        "url"  => "http://127.0.0.1",
        "debug" => true,
        // 控制器的命名空间
        "controllerNamespace" => "PHPSeed\\Controller\\",
        "upload" => [
            "image" => [
                // 本地路径
                "localPath" => __DIR__ . "/../upload/image/",
                // 远程路径
                "remotePath" => "",
                "min" => "1KB",
                "max" => "10MB",
            ],
            "video" => [
                "localPath" => __DIR__ . "/../upload/video/",
                "min" => "1KB",
                "max" => "100MB",
            ],
        ],
    ],
    "datasource" => [
        "mysql" => [
            "host" => "127.0.0.1",
            "port" => 3306,
            "database" => "digitalduhu",
            "username" => "root",
            "password" => "root",
            "charset" => "utf8mb4",
            "collation" => "utf8mb4_unicode_ci",
            "prefix"    => "",
        ],
        "redis" => [
            "scheme" => "tcp",
            "host" => "127.0.0.1",
            "port" => 6379,
            // 数据库索引（默认为0）
            "cache" => 0,
            "password" => "root",
        ]
    ],
    "jwt" => [
        // 请求头或请求参数的key
        "header" => "Authorization",
        // 签发人
        "issuedBy" => "seed",
        // 受众
        "permittedFor" => "member",
        // 签发id
        "identifiedBy" => "123",
        // 多久过期（分钟）
        "expiresMinutes" => "1",
        // 私钥
        "signingKey" => __DIR__ . "/../rsa/private-key.pem",
        // 公钥
        "verificationKey" => "MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAKw+D9cjGEbEuGEhGwe1dy0LP/ujK02wHZ5RfAnWp4Hg/PYEa6fbM/DLrSNbNsTj56Wr0r/B3gd1acBNSMNVitkCAwEAAQ==",
    ]
];

return json_decode(json_encode($config), FALSE);
