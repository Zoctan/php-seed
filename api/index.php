<?php

require_once __DIR__ . "/vendor/autoload.php";

use Predis\Client;
use Medoo\Medoo;
use PHPSeed\Core\DI;
use PHPSeed\Core\Http\Session;
use PHPSeed\Core\Http\Request;
use PHPSeed\Core\Http\Response;
use PHPSeed\Core\Filter;
use PHPSeed\Core\Filter\AuthenticationFilter;
use PHPSeed\Core\Filter\CorsFilter;
use PHPSeed\Core\Exception\ExceptionHandler;

/**
 * 启动应用
 */
function bootApp()
{
    date_default_timezone_set("prc");
    
    $di = DI::getInstance();

    // 初始化配置
    $di->config = require_once __DIR__ . "/config.php";

    // 注册全局异常处理器
    $di->exceptionHandler = new ExceptionHandler();

    // 捕获全局请求
    $di->request = Request::capture();

    // 注册响应
    $di->response = new Response();

    // 注册数据库连接
    $mysqlConfig = json_decode(json_encode($di->config->datasource->mysql), true);
    $di->mysql = new Medoo($mysqlConfig);

    // 注册缓存工具
    $di->cache = new Client($di->config->datasource->redis);
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
    new AuthenticationFilter($router->getRoutes()),
);

// 路由分发、处理请求、返回响应
$router->dispatch($di->request);
