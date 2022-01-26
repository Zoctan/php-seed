<?php

//ini_set("display_errors", false);

require_once __DIR__ . "/vendor/autoload.php";

use App\Core\Http\Session;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Filter;
use App\Core\Filter\CorsFilter;
use App\Core\Filter\RequestContentTypeFilter;
use App\Core\Filter\AuthenticationFilter;
use App\Core\Exception\ExceptionHandler;

/**
 * 启动应用
 */
function bootApp()
{
    // 注册全局异常处理器
    //ExceptionHandler::register();

    date_default_timezone_set("prc");

    $di = \App\DI();

    // 初始化配置
    $di->config = require_once __DIR__ . "/config.php";

    // 捕获全局请求
    $di->request = Request::capture();

    // 注册响应
    $di->response = new Response();

    // 注册缓存工具
    $di->cache = new Predis\Client((array) $di->config->datasource->redis);

    // 注册数据检查工具：https://respect-validation.readthedocs.io/en/latest/concrete-api/
    // 规则：https://respect-validation.readthedocs.io/en/latest/list-of-rules/
    $di->validator = new Respect\Validation\Validator();

    // 注册伪造数据工具：https://github.com/fzaninotto/Faker
    $di->faker = Faker\Factory::create("zh_CN");

    // 注册 HTTP 客户端：https://docs.guzzlephp.org/en/stable/quickstart.html
    $di->curl = new \GuzzleHttp\Client();
    return $di;
}

// restful api 用不上 session
function initSession($di)
{
    $session = new Session();
    $session->start();
    $di->session = $session;
}

// 执行过滤链
function doFilterChain(Filter ...$filters)
{
    foreach ($filters as $filter) {
        $filter->doFilter();
    }
}

// 启动应用
$di = bootApp();

// 注册路由
$router = require_once __DIR__ . "/routes.php";

// 按顺序执行过滤链
doFilterChain(
    new CorsFilter(),
    new RequestContentTypeFilter(),
    new AuthenticationFilter($router->getRoutes()),
);

var_dump($request);
//var_dump($di->request->getPath());
// 路由分发、处理请求、返回响应
$router->dispatch($di->request);
