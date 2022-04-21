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
    // controller config
    'controller' => [
        // response cofig
        'response' => [
            // response data structure map
            'structureMap' => [
                // status code key
                'errno' => 'errno',
                // message key
                'msg'   => 'msg',
                // data key
                'data'  => 'data',
                // debug key
                'debug' => 'debug',
            ],
        ],
    ],
    // router config
    'router' => [
        // router.php path
        'path' => $basePath . '/router.php',
        // routes.cache path
        'cachePath' => $basePath . '/routes-development.cache',
    ],
    // upload config
    'upload' => [
        // UploadController->download method route
        'downloadUrl' => 'http://127.0.0.1/php-seed/upload/',
        // image file type
        'image' => [
            // local disk path
            'localPath' => 'upload/image',
            // remote network path
            'remotePath' => '',
            // allow mime type list
            'allowMimeType' => ['image/jpeg', 'image/png', 'image/gif'],
            // compress config
            'compressConfig' => [
                'quality' => 70,
            ],
            // watermark config
            'watermarkConfig' => [
                'path' => $basePath . '/upload/image/logo.png',
                'position' => 'bottom-right',
                'x' => 10,
                'y' => 10,
                // (pixel)
                'width' => 600,
                'height' => 200,
                // original image height : watermark height
                'heightScale' => '15:1',
            ],
            // minimum file size(kb)
            'minKB' => 1,
            // maximum file size(kb)
            'maxKB' => 100 * 1024,
        ],
        'video' => [
            'localPath' => 'upload/video',
            'remotePath' => '',
            'allowMimeType' => ['video/mp4', 'video/3gpp', 'video/x-msvideo', 'video/mpeg', 'video/quicktime'],
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
        'expiresMinutes' => 30,
        // private key
        'signingKey' => $basePath . '/rsa/private-key.pem',
        // public key
        'verificationKey' => 'MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAKw+D9cjGEbEuGEhGwe1dy0LP/ujK02wHZ5RfAnWp4Hg/PYEa6fbM/DLrSNbNsTj56Wr0r/B3gd1acBNSMNVitkCAwEAAQ==',
    ]
];
