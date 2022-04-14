<?php

return [
    'app' => [
        'name' => 'phpseed-production',
        'description' => 'PHP Seed Production',
        'baseUrl' => 'http://127.0.0.1/php-seed',
        'routerPath' => $basePath . '/router.php',
        'routesCachePath' => $basePath . '/routes-production.cache',
        'response' => [
            'type' => 'json',
            'structureMap' => [
                'errno' => 'errno',
                'msg'   => 'msg',
                'data'  => 'data',
                'debug' => 'debug',
            ],
        ],
        'controllerNamespace' => 'App\\Controller\\',
        'upload' => [
            'image' => [
                'localPath' => 'upload/image',
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
                    'type' => 'mysql',
                    'host' => 'localhost',
                    'database' => 'phpseed',
                    'username' => 'root',
                    'password' => 'root',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'port' => 3306,
                    'logging' => true,
                    'error' => PDO::ERRMODE_SILENT,
                    'option' => [
                        PDO::ATTR_CASE => PDO::CASE_NATURAL
                    ],
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
            'cache' => 0,
            'password' => 'root',
        ]
    ],
    'jwt' => [
        'header' => 'Authorization',
        'issuedBy' => 'phpseed',
        'permittedFor' => 'member',
        'identifiedBy' => '123',
        'refreshMinutes' => 1440,
        'expiresMinutes' => 1,
        'signingKey' => $basePath . '/rsa/private-key.pem',
        'verificationKey' => 'MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAKw+D9cjGEbEuGEhGwe1dy0LP/ujK02wHZ5RfAnWp4Hg/PYEa6fbM/DLrSNbNsTj56Wr0r/B3gd1acBNSMNVitkCAwEAAQ==',
    ]
];
