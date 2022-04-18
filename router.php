<?php

namespace App;

use App\Core\Http\Router;

$env = \App\DI()->config['env'];
$routesCachePath = \App\DI()->config['router']['cachePath'];

$router = new Router();

if ($env === 'production') {
  $router->loadCache($routesCachePath);
  return $router;
}

if ($env === 'development') {
  $router->addRoute('*', '/', 'HomeController@home', ['auth' => false]);

  $router->addGroup('/member', 'MemberController')
    ->addRoute('POST', '/isMemberExist', 'isMemberExist', ['auth' => false])
    ->addRoute('POST', '/validateOldPassword', 'validateOldPassword')
    ->addRoute('POST', '/register', 'register', ['auth' => false])
    ->addRoute(['GET', 'POST'], '/login', 'login', ['auth' => false])
    ->addRoute(['GET', 'POST'], '/validateAccessToken', 'validateAccessToken', ['auth' => false])
    ->addRoute('DELETE', '/logout', 'logout', ['auth' => false])
    ->addRoute('PUT', '/refreshAccessToken', 'refreshAccessToken', ['auth' => false])
    ->addRoute(['GET', 'POST'], '/detail', 'detail')
    ->addRoute(['GET', 'POST'], '/profile', 'profile')
    ->addRoute('POST', '/list', 'list', ['permission' => ['member:list']])
    ->addRoute('PUT', '/updatePassword', 'updatePassword')
    ->addRoute('PUT', '/updateProfile', 'updateProfile')
    ->addRoute('PUT', '/updateDetail', 'updateDetail')
    ->addRoute('POST', '/add', 'add')
    ->addRoute('DELETE', '/delete', 'delete');

  $router->addGroup('/role', 'RoleController')
    ->addRoute('POST', '/list', 'list')
    ->addRoute('POST', '/listParent', 'listParent')
    ->addRoute(['GET', 'POST'], '/detail', 'detail')
    ->addRoute('POST', '/add', 'add')
    ->addRoute('PUT', '/update', 'update')
    ->addRoute('DELETE', '/delete', 'delete')
    ->addRoute('POST', '/addMemberRole', 'addMemberRole')
    ->addRoute('DELETE', '/deleteMemberRole', 'deleteMemberRole');

  $router->addGroup('/rule', 'RuleController')
    ->addRoute('POST', '/list', 'list')
    ->addRoute('POST', '/add', 'add')
    ->addRoute('PUT', '/updateList', 'updateList')
    ->addRoute('PUT', '/update', 'update')
    ->addRoute('DELETE', '/deleteList', 'deleteList')
    ->addRoute('DELETE', '/delete', 'delete');

  $router->addGroup('/upload', 'UploadController')
    ->addRoute('GET', '', 'download', ['auth' => false])
    ->addRoute(['GET', 'POST'], '/add', 'add')
    ->addRoute(['GET', 'POST', 'DELETE'], '/delete', 'delete');

  $router->addGroup('/pair', 'PairController')
    ->addRoute('POST', '/list', 'list')
    ->addRoute('POST', '/getValue', 'getValue')
    ->addRoute('POST', '/add', 'add')
    ->addRoute('PUT', '/update', 'update')
    ->addRoute('DELETE', '/delete', 'delete');

  $router->addGroup('/log', 'LogController')
    ->addRoute('POST', '/list', 'list')
    ->addRoute('DELETE', '/delete', 'delete');

  $router->addGroup('/fake', 'FakeController')
    ->addRoute('GET', '/getName', 'getName');

  $router->cache($routesCachePath);

  return $router;
}
