<?php

namespace App;

use App\Core\Router\Router;

$env = \App\DI()->config['env'];
$routesCachePath = \App\DI()->config['router']['cachePath'];

$router = new Router();

if ($env === 'production') {
  $router->loadCache($routesCachePath);
  return $router;
}

namespace App\Controller;

use App\Core\Result\Result;
use App\Core\Response\mimeType;

if ($env === 'development') {
  // use Controller::class and method string to add route
  $router->addGroup('', HomeController::class)
    ->addRoute('*', '/', 'home', ['auth' => false])
    ->addRoute('*', '/demoDifferentMimeType', 'demoDifferentMimeType', ['auth' => false, 'mimeType' => MimeType::XML]);

  // use Controller@method to add route
  // clear group
  $router->addGroup();
  $router->addRoute('*', '/demo1', 'HomeController@demo1', ['auth' => false]);

  // use Controller instance and method string to add route
  $router->addGroup('', new HomeController())
    ->addRoute('*', '/demo2', 'demo2', ['auth' => false]);

  // use closure function to add route
  // note: don't use closure function when cache route
  $router->addGroup();
  $router->addRoute('*', '/demo3', function () {
    return Result::success('demo3');
  }, ['auth' => false]);

  $router->addGroup('/member', MemberController::class)
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
    ->addRoute('DELETE', '/remove', 'remove');

  $router->addGroup('/role', RoleController::class)
    ->addRoute('POST', '/list', 'list')
    ->addRoute('POST', '/listParent', 'listParent')
    ->addRoute(['GET', 'POST'], '/detail', 'detail')
    ->addRoute('POST', '/add', 'add')
    ->addRoute('PUT', '/update', 'update')
    ->addRoute('DELETE', '/remove', 'remove')
    ->addRoute('POST', '/addMemberRole', 'addMemberRole')
    ->addRoute('DELETE', '/removeMemberRole', 'removeMemberRole');

  $router->addGroup('/rule', RuleController::class)
    ->addRoute('POST', '/list', 'list')
    ->addRoute('POST', '/add', 'add')
    ->addRoute('PUT', '/updateList', 'updateList')
    ->addRoute('PUT', '/update', 'update')
    ->addRoute('DELETE', '/removeList', 'removeList')
    ->addRoute('DELETE', '/remove', 'remove');

  $router->addGroup('/upload', UploadController::class)
    ->addRoute('GET', '', 'download', ['auth' => false])
    ->addRoute(['GET', 'POST'], '/add', 'add')
    ->addRoute(['GET', 'POST', 'DELETE'], '/remove', 'remove');

  $router->addGroup('/pair', PairController::class)
    ->addRoute('POST', '/list', 'list')
    ->addRoute('POST', '/getValue', 'getValue')
    ->addRoute('POST', '/add', 'add')
    ->addRoute('PUT', '/update', 'update')
    ->addRoute('DELETE', '/remove', 'remove');

  $router->addGroup('/log', LogController::class)
    ->addRoute('POST', '/list', 'list')
    ->addRoute('DELETE', '/remove', 'remove');

  $router->addGroup('/fake', FakeController::class)
    ->addRoute('GET', '/getName', 'getName');

  // $router->cache($routesCachePath);

  return $router;
}
