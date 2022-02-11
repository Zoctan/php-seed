<?php

require_once __DIR__ . "/../api/vendor/autoload.php";

use GuzzleHttp\Client;

$client = new Client([
    // Base URI is used with relative requests
    'base_uri' => 'http://127.0.0.1/php-seed/api/',
]);

// $body = [
//     "username" => "admin",
//     "password" => "admin"
// ];
// $response = $client->request('POST', 'member/login', ["json" => $body]);
// $body = $response->getBody();
// var_dump(json_decode($body, false));

$headers = [
   "Authorization" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJzZWVkIiwiYXVkIjoibWVtYmVyIiwianRpIjoiMTIzIiwiaWF0IjoxNjQzMTkyODc5LjAzMTk2MSwiZXhwIjoxNjQzMTkzNzc5LjAzMTk2MSwibWVtYmVySWQiOjIsInJvbGUiOmZhbHNlLCJvcGVyYXRlIjpbXX0.Xkf3EpJJ8UCZLie3zOXx7-XHQ2Ng_e_qm6RoUyHLFW7PoQxg2wEHkV5xgPSYEknjpvj2MG5oEC3h4odFPLEYpA"
];
$response = $client->request('POST', 'member/list', ["headers" => $headers]);
foreach ($response->getHeaders() as $name => $values) {
    echo $name . ': ' . implode(', ', $values) . "\r\n";
}
var_dump(json_decode($response->getBody(), false));
