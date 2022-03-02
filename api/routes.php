<?php

namespace App;

use App\Core\Http\Router;

$router = new Router();

$router->register("POST", "/member/checkExist", "MemberController@checkExist");
$router->register("POST", "/member/checkOldPassword", "MemberController@checkOldPassword")->requiresAuth();
$router->register("POST", "/member/register", "MemberController@register");
$router->register(["GET", "POST"], "/member/login", "MemberController@login");
$router->register("DELETE", "/member/logout", "MemberController@logout")->requiresAuth();
$router->register("PUT", "/member/refreshToken", "MemberController@refreshToken")->requiresAuth();
$router->register("GET", "/member/detail", "MemberController@detail")->requiresAuth();
$router->register("GET", "/member/profile", "MemberController@profile")->requiresAuth();
$router->register("POST", "/member/list", "MemberController@list")->requiresAuth();
$router->register("PUT", "/member/updatePassword", "MemberController@updatePassword")->requiresAuth();
$router->register("PUT", "/member/updateProfile", "MemberController@updateProfile")->requiresAuth();
$router->register("PUT", "/member/updateDetail", "MemberController@updateDetail")->requiresAuth();
$router->register("DELETE", "/member/delete", "MemberController@delete")->requiresAuth();

return $router;
