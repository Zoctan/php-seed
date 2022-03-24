<?php

$config = [
    "app" => [
        "name" => "PHPSeed",
        "description" => "PHP 种子",
        "url"  => "http://127.0.0.1",
        "debug" => true,
        "response" => [
            "type" => "json",
            // 响应结构字段映射
            "structureMap" => [
                "errno" => "errno",
                "data"  => "data",
                "msg"   => "msg",
                "debug" => "debug",
            ],
        ],
        // 控制器的命名空间
        "controllerNamespace" => "App\\Controller\\",
        "upload" => [
            "image" => [
                // 本地路径
                "localPath" => __DIR__ . "/upload/image/",
                // 远程路径
                "remotePath" => "",
                "min" => "1KB",
                "max" => "10MB",
            ],
            "video" => [
                "localPath" => __DIR__ . "/upload/video/",
                "min" => "1KB",
                "max" => "100MB",
            ],
        ],
    ],
    "datasource" => [
        "mysql" => [
            "master" => [
                [
                    // https://medoo.in/api/new
                    "type" => "mysql",
                    "host" => "localhost",
                    "database" => "phpseed",
                    "username" => "root",
                    "password" => "root",

                    // [optional]
                    "charset" => "utf8mb4",
                    "collation" => "utf8mb4_unicode_ci",
                    "port" => 3306,

                    // [optional] Table prefix, all table names will be prefixed as PREFIX_table.
                    //"prefix" => "PREFIX_",

                    // [optional] Enable logging, it is disabled by default for better performance.
                    "logging" => true,

                    // [optional]
                    // Error mode
                    // Error handling strategies when error is occurred.
                    // PDO::ERRMODE_SILENT (default) | PDO::ERRMODE_WARNING | PDO::ERRMODE_EXCEPTION
                    // Read more from https://www.php.net/manual/en/pdo.error-handling.php.
                    "error" => PDO::ERRMODE_SILENT,

                    // [optional]
                    // The driver_option for connection.
                    // Read more from http://www.php.net/manual/en/pdo.setattribute.php.
                    "option" => [
                        // PDO::ATTR_CASE：强制列名为指定的大小写
                        //      PDO::CASE_NATURAL：保留数据库驱动返回的列名
                        PDO::ATTR_CASE => PDO::CASE_NATURAL
                    ],

                    // [optional] Medoo will execute those commands after connected to the database.
                    "command" => [
                        "SET SQL_MODE=ANSI_QUOTES"
                    ]
                ]
            ],
            "slave" => [
                []
            ]
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
        // 刷新时间（分钟）：这段时间内可以获取新 token
        "refreshMinutes" => 1440,
        // 多久过期（分钟）
        "expiresMinutes" => 30,
        // 私钥
        "signingKey" => __DIR__ . "/rsa/private-key.pem",
        // 公钥
        "verificationKey" => "MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAKw+D9cjGEbEuGEhGwe1dy0LP/ujK02wHZ5RfAnWp4Hg/PYEa6fbM/DLrSNbNsTj56Wr0r/B3gd1acBNSMNVitkCAwEAAQ==",
    ],
    "wechat" => [
        "sslCert" => __DIR__ . "/cert/cacert.pem",
        "credential" => [
            "appId" => "wx07f8fd1b50ae8109",
            "appSecret" => "aab76a401717a502eb32aa4b37f96570"
        ]
    ]
];

return json_decode(json_encode($config), false);
