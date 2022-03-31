<?php

return [
    'app' => [
        'name' => 'phpseed',
        'description' => 'PHP SEED',
        'url'  => 'http://127.0.0.1/php-seed',
        'projectPath' => __DIR__,
        'debug' => true,
        'response' => [
            'type' => 'json',
            // 响应结构字段映射
            'structureMap' => [
                'errno' => 'errno',
                'data'  => 'data',
                'msg'   => 'msg',
                'debug' => 'debug',
            ],
        ],
        // 控制器的命名空间
        'controllerNamespace' => 'App\\Controller\\',
        'upload' => [
            'image' => [
                // 本地相对路径
                'localPath' => 'upload/image',
                // 远程路径
                'remotePath' => '',
                'allowType' => ['image/jpeg', 'image/png', 'image/gif'],
                'minKB' => 1,
                'maxKB' => 100 * 1024,
            ],
            'video' => [
                'localPath' => 'upload/video',
                'remotePath' => '',
                'allowType' => ['video/mp4', 'video/3gpp', 'video/x-msvideo', 'video/mpeg', 'video/quicktime'],
                'minKB' => 1,
                'maxKB' => 100 * 1024,
            ],
        ],
    ],
    'datasource' => [
        'mysql' => [
            'master' => [
                [
                    // https://medoo.in/api/new
                    'type' => 'mysql',
                    'host' => 'localhost',
                    'database' => 'phpseed',
                    'username' => 'root',
                    'password' => 'root',

                    // [optional]
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'port' => 3306,

                    // [optional] Table prefix, all table names will be prefixed as PREFIX_table.
                    //'prefix' => 'PREFIX_',

                    // [optional] Enable logging, it is disabled by default for better performance.
                    'logging' => true,

                    // [optional]
                    // Error mode
                    // Error handling strategies when error is occurred.
                    // PDO::ERRMODE_SILENT (default) | PDO::ERRMODE_WARNING | PDO::ERRMODE_EXCEPTION
                    // Read more from https://www.php.net/manual/en/pdo.error-handling.php.
                    'error' => PDO::ERRMODE_SILENT,

                    // [optional]
                    // The driver_option for connection.
                    // Read more from http://www.php.net/manual/en/pdo.setattribute.php.
                    'option' => [
                        // PDO::ATTR_CASE：强制列名为指定的大小写
                        //      PDO::CASE_NATURAL：保留数据库驱动返回的列名
                        PDO::ATTR_CASE => PDO::CASE_NATURAL
                    ],

                    // [optional] Medoo will execute those commands after connected to the database.
                    'command' => [
                        'SET SQL_MODE=ANSI_QUOTES'
                    ]
                ]
            ],
            'slave' => [
                []
            ]
        ],
        'redis' => [
            'scheme' => 'tcp',
            'host' => '127.0.0.1',
            'port' => 6379,
            // 数据库索引（默认为0）
            'cache' => 0,
            'password' => 'root',
        ]
    ],
    'jwt' => [
        // 请求头或请求参数的key
        'header' => 'Authorization',
        // 签发人
        'issuedBy' => 'phpseed',
        // 受众
        'permittedFor' => 'member',
        // 签发id
        'identifiedBy' => '123',
        // 刷新时间（分钟）：这段时间内可以获取新 token
        'refreshMinutes' => 1440,
        // 多久过期（分钟）
        'expiresMinutes' => 60,
        // 私钥
        'signingKey' => __DIR__ . '/rsa/private-key.pem',
        // 公钥
        'verificationKey' => 'MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAKw+D9cjGEbEuGEhGwe1dy0LP/ujK02wHZ5RfAnWp4Hg/PYEa6fbM/DLrSNbNsTj56Wr0r/B3gd1acBNSMNVitkCAwEAAQ==',
    ],
    'wechat' => [
        'sslCert' => __DIR__ . '/cert/cacert.pem',
        'credential' => [
            'appId' => '',
            'appSecret' => ''
        ]
    ]
];
