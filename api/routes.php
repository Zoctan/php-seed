<?php

namespace App;

use App\Core\Http\Router;

$router = new Router();

$router->register(["POST"], "login", "MemberController@login", false);

return $router;
