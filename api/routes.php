<?php

namespace App;

use App\Core\Http\Router;

$router = new Router();

$router->register("POST", "/member/register", "MemberController@register");
$router->register(["GET", "POST"], "/member/login", "MemberController@login");
$router->register("DELETE", "/member/logout", "MemberController@logout");
$router->register("PUT", "/member/refreshToken", "MemberController@refreshToken");
$router->register("GET", "/member/profile", "MemberController@profile")->requiresAuth();
$router->register("POST", "/member/list", "MemberController@list");
$router->register("PUT", "/member/update", "MemberController@update");
$router->register("DELETE", "/member/delete", "MemberController@delete");

return $router;
