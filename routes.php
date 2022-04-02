<?php

namespace App;

use App\Core\Http\Router;

$router = new Router();

$router->register('POST', '/member/checkExist', 'MemberController@checkExist')->notRequiresAuth();
$router->register('POST', '/member/checkOldPassword', 'MemberController@checkOldPassword');
$router->register('POST', '/member/register', 'MemberController@register')->notRequiresAuth();
$router->register(['GET', 'POST'], '/member/login', 'MemberController@login')->notRequiresAuth();
$router->register('DELETE', '/member/logout', 'MemberController@logout');
$router->register('PUT', '/member/refreshToken', 'MemberController@refreshToken');
$router->register(['GET', 'POST'], '/member/detail', 'MemberController@detail');
$router->register(['GET', 'POST'], '/member/profile', 'MemberController@profile');
$router->register('POST', '/member/list', 'MemberController@list');
$router->register('PUT', '/member/updatePassword', 'MemberController@updatePassword');
$router->register('PUT', '/member/updateProfile', 'MemberController@updateProfile');
$router->register('PUT', '/member/updateDetail', 'MemberController@updateDetail');
$router->register('POST', '/member/add', 'MemberController@add');
$router->register('DELETE', '/member/delete', 'MemberController@delete');

$router->register('POST', '/role/add', 'RoleController@add');
$router->register('POST', '/role/list', 'RoleController@list');
$router->register(['GET', 'POST'], '/role/detail', 'RoleController@detail');
$router->register('PUT', '/role/update', 'RoleController@update');
$router->register('PUT', '/memberRole/update', 'RoleController@updateMemberRole');
$router->register('DELETE', '/role/delete', 'RoleController@delete');

$router->register('POST', '/rule/add', 'RuleController@add');
$router->register('POST', '/rule/list', 'RuleController@list');
$router->register('PUT', '/rule/updateList', 'RuleController@updateList');
$router->register('PUT', '/rule/update', 'RuleController@update');
$router->register('DELETE', '/rule/deleteList', 'RuleController@deleteList');
$router->register('DELETE', '/rule/delete', 'RuleController@delete');

$router->register('GET', '/upload', 'UploadController@download');
$router->register(['GET', 'POST'], '/upload/add', 'UploadController@add');
$router->register(['GET', 'POST', 'DELETE'], '/upload/delete', 'UploadController@delete');

$router->register('POST', '/system/getValue', 'SystemController@getValue');
$router->register('POST', '/system/add', 'SystemController@add');
$router->register('PUT', '/system/update', 'SystemController@update');
$router->register('DELETE', '/system/delete', 'SystemController@delete');
return $router;
