<?php

return [
    "app" => [
        "name" => "学院君",
        "desc" => "让学习与进取者不再孤独",
        "url"  => "https://xueyuanjun.com",
        "basePath" => __DIR__ . "/../../",
        "database" => [
            "type" => "mysql",
            "host" => "127.0.0.1",
            "port" => 3306,
            "database" => "digitalduhu",
            "username" => "root",
            "password" => "root",
            "charset" => "utf8mb4",
            "collation" => "utf8mb4_unicode_ci",
            "prefix"    => "",
        ],
    ],
    "session" => [
        "lifetime" => 2 * 60 * 60
    ]
];
