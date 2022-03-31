<?php

namespace App;

use App\Core\Http\Router;

$router = new Router();

$router->register("POST", "/member/checkExist", "MemberController@checkExist");
$router->register("POST", "/member/checkOldPassword", "MemberController@checkOldPassword")->requiresAuth();
$router->register("POST", "/member/register", "MemberController@register");
$router->register(["GET", "POST"], "/member/login", "MemberController@login");
$router->register("DELETE", "/member/logout", "MemberController@logout")->requiresAuth();
$router->register("PUT", "/member/refreshToken", "MemberController@refreshToken");
$router->register(["GET", "POST"], "/member/detail", "MemberController@detail")->requiresAuth();
$router->register(["GET", "POST"], "/member/profile", "MemberController@profile")->requiresAuth();
$router->register("POST", "/member/list", "MemberController@list")->requiresAuth();
$router->register("PUT", "/member/updatePassword", "MemberController@updatePassword")->requiresAuth();
$router->register("PUT", "/member/updateProfile", "MemberController@updateProfile")->requiresAuth();
$router->register("PUT", "/member/updateDetail", "MemberController@updateDetail")->requiresAuth();
$router->register("POST", "/member/add", "MemberController@add")->requiresAuth();
$router->register("DELETE", "/member/delete", "MemberController@delete")->requiresAuth();

$router->register("POST", "/role/add", "RoleController@add")->requiresAuth();
$router->register("POST", "/role/list", "RoleController@list")->requiresAuth();
$router->register(["GET", "POST"], "/role/detail", "RoleController@detail")->requiresAuth();
$router->register("PUT", "/role/update", "RoleController@update")->requiresAuth();
$router->register("PUT", "/memberRole/update", "RoleController@updateMemberRole")->requiresAuth();
$router->register("DELETE", "/role/delete", "RoleController@delete")->requiresAuth();

$router->register("POST", "/rule/add", "RuleController@add")->requiresAuth();
$router->register("POST", "/rule/list", "RuleController@list")->requiresAuth();
$router->register("PUT", "/rule/updateList", "RuleController@updateList")->requiresAuth();
$router->register("PUT", "/rule/update", "RuleController@update")->requiresAuth();
$router->register("DELETE", "/rule/deleteList", "RuleController@deleteList")->requiresAuth();
$router->register("DELETE", "/rule/delete", "RuleController@delete")->requiresAuth();

$router->register("GET", "/upload", "UploadController@download");
$router->register(["GET", "POST"], "/upload/add", "UploadController@add")->requiresAuth();
$router->register(["GET", "POST", "DELETE"], "/upload/delete", "UploadController@delete")->requiresAuth();

$router->register("POST", "/system/getValue", "SystemController@getValue")->requiresAuth();
$router->register("POST", "/system/add", "SystemController@add")->requiresAuth();
$router->register("PUT", "/system/update", "SystemController@update")->requiresAuth();
$router->register("DELETE", "/system/delete", "SystemController@delete")->requiresAuth();
return $router;
