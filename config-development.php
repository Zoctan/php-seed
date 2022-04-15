<?php

return [
    // enable global debug
    'debug' => true,
    // api base url
    'baseUrl' => 'http://127.0.0.1/php-seed',
    'app' => [
        'name' => 'phpseed-development',
        'description' => 'PHP Seed Development',
    ],
    // router config
    'router' => [
        // router.php saving path
        'path' => $basePath . '/router.php',
        // routes.cache saving path
        'cachePath' => $basePath . '/routes-development.cache',
    ],
    // upload config
    'upload' => [
        // image file type
        'image' => [
            // local disk path
            'localPath' => 'upload/image',
            // remote network path
            'remotePath' => '',
            // allow type list
            'allowType' => ['image/jpeg', 'image/png', 'image/gif'],
            // minimum file size(kb)
            'minKB' => 1,
            // maximum file size(kb)
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
    // datasource config
    'datasource' => [
        // mysql config
        'mysql' => [
            // master database config list
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
                    // Read more from https://www.php.net/manual/en/pdo.error-handling.php
                    'error' => PDO::ERRMODE_SILENT,

                    // [optional]
                    // The driver_option for connection.
                    // Read more from https://www.php.net/manual/en/pdo.setattribute.php
                    'option' => [
                        // PDO::ATTR_CASE: (force column names to a specific case)
                        //      PDO::CASE_NATURAL (leave column names as returned by the database driver)
                        PDO::ATTR_CASE => PDO::CASE_NATURAL
                    ],

                    // [optional] Medoo will execute those commands after connected to the database.
                    'command' => [
                        'SET SQL_MODE=ANSI_QUOTES'
                    ]
                ]
            ],
            // slave database config list
            'slave' => [
                []
            ]
        ],
        // redis config
        'redis' => [
            // scheme
            'scheme' => 'tcp',
            // host
            'host' => '127.0.0.1',
            // port
            'port' => 6379,
            // cache database index (default:0)
            'cache' => 0,
            // password
            'password' => 'root',
        ]
    ],
    // jwt config
    'jwt' => [
        // key in request header or data
        'header' => 'Authorization',
        // issued by
        'issuedBy' => 'phpseed',
        // permitted for
        'permittedFor' => 'member',
        // identified by
        'identifiedBy' => '123',
        // refresh time (minute): can get new access token in this period
        'refreshMinutes' => 1440,
        // expires time (minute)
        'expiresMinutes' => 1,
        // private key
        'signingKey' => $basePath . '/rsa/private-key.pem',
        // public key
        'verificationKey' => 'MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAKw+D9cjGEbEuGEhGwe1dy0LP/ujK02wHZ5RfAnWp4Hg/PYEa6fbM/DLrSNbNsTj56Wr0r/B3gd1acBNSMNVitkCAwEAAQ==',
    ]
];
