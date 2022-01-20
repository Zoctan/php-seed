<?php

namespace PHPSeed;

use PHPSeed\Core\Http\Router;

$router = new Router();

$router->register(["get", "post"], "/", "TestController@show", true);

return $router;
