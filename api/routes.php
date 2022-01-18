<?php

namespace PHPSeed;

use PHPSeed\Core\Http\Router;

$router = new Router();

$request = $container->resolve("request");

$router->register(["get", "post"], "/", "TestController@show");

return $router;
