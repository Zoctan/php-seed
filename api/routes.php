<?php

namespace App;

use App\Core\Http\Router;

$router = new Router();

$router->register(["get", "post"], "/", "TestController@show", true);

$router->register("get", "jssdk", "TestController@jssdk", false);

return $router;
