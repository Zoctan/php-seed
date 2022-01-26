<?php

namespace App;

use App\Core\Http\Router;

$router = new Router();

$router->register(["GET", "POST"], "/member/login", "MemberController@login", false);
$router->register(["GET", "POST"], "/member/list", "MemberController@list", true);

return $router;
