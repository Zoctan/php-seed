<?php

use App\Core\Response\MimeType;

return [
    // enable which env
    'env' => 'development',
    // project base path
    'basePath' => __DIR__,
    // app config
    'app' => [
        // app name
        'name' => 'phpseed',
        // app description
        'description' => 'PHP Seed',
    ],
    // controller config
    'controller' => [
        // class namespace
        'namespace' => 'App\\Controller\\',
        // response cofig
        'response' => [
            // response data type
            'mimeType' => MimeType::JSON,
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
    ]
];
