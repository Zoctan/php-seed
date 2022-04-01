<?php

ini_set('display_errors', false);

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Filter;
use App\Core\Collection;
use App\Core\Filter\CorsFilter;
use App\Core\Filter\AuthenticationFilter;
use App\Core\Exception\ExceptionHandler;

/**
 * 启动应用
 */
function bootApp()
{
    // 注册全局异常处理器
    ExceptionHandler::register();

    // 设置时区
    date_default_timezone_set('prc');

    $di = \App\DI();

    // 初始化配置
    $di->config = new Collection(require_once __DIR__ . '/config.php');

    // 捕获全局请求
    $di->request = new Request();

    // 注册响应
    $di->response = (new Response())
        ->isDebug($di->config['app']['debug'])
        ->setDebugKey($di->config['app']['response']['structureMap']['debug'])
        ->setResponseType($di->config['app']['response']['type']);

    // 注册缓存工具
    $di->cache = new Predis\Client($di->config['datasource']['redis']);

    // 注册伪造数据工具
    $di->faker = Faker\Factory::create('zh_CN');

    // 注册 HTTP 客户端
    $di->curl = new GuzzleHttp\Client();

    // 注册图片处理工具
    $di->image = new Intervention\Image\ImageManager(['driver' => 'imagick']);

    return $di;
}

// 执行过滤链
function doFilterChain(Filter ...$filters)
{
    foreach ($filters as $filter) {
        if (!$filter->doFilter()) {
            break;
        }
    }
}

// 启动应用
$di = bootApp();

// 注册路由
$router = require_once __DIR__ . '/routes.php';

// 按顺序执行过滤链
doFilterChain(
    new CorsFilter(),
    new AuthenticationFilter($router->getRoutes()),
);

// 路由分发、处理请求、返回响应
$router->dispatch($di->request);
