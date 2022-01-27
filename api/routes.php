<?php

namespace App;

use App\Core\Http\Router;

$router = new Router();

$router->register(["GET", "POST"], "/member/login", "MemberController@login", null);
$router->register(["GET", "POST"], "/member/list", "MemberController@list", "member:list");

return $router;
