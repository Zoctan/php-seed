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
$router->register(["GET", "POST"], "/member/detail", "MemberController@detail")->requiresAuth();
$router->register(["GET", "POST"], "/member/profile", "MemberController@profile")->requiresAuth();
$router->register("POST", "/member/list", "MemberController@list")->requiresAuth();
$router->register("PUT", "/member/updatePassword", "MemberController@updatePassword")->requiresAuth();
$router->register("PUT", "/member/updateProfile", "MemberController@updateProfile")->requiresAuth();
$router->register("PUT", "/member/updateDetail", "MemberController@updateDetail")->requiresAuth();
$router->register("POST", "/member/add", "MemberController@add")->requiresAuth();
$router->register("DELETE", "/member/delete", "MemberController@delete")->requiresAuth();

$router->register("POST", "/role/add", "RoleController@addRole")->requiresAuth();
$router->register("POST", "/rule/add", "RoleController@addRule")->requiresAuth();
$router->register("POST", "/role/list", "RoleController@listRole")->requiresAuth();
$router->register("POST", "/rule/list", "RoleController@listRule")->requiresAuth();
$router->register(["GET", "POST"], "/role/detail", "RoleController@detail")->requiresAuth();
$router->register("PUT", "/role/update", "RoleController@updateRole")->requiresAuth();
$router->register("PUT", "/rule/update", "RoleController@updateRule")->requiresAuth();
$router->register("PUT", "/memberRole/update", "RoleController@updateMemberRole")->requiresAuth();
$router->register("DELETE", "/role/delete", "RoleController@delete")->requiresAuth();

return $router;
