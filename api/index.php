<?php

require_once __DIR__ . '/vendor/autoload.php';

use PHPSeed\Core\Container;
use PHPSeed\Core\Http\Session;
use PHPSeed\Core\Http\Request;
use PHPSeed\Core\Http\Response;
use PHPSeed\Core\BaseException;
use PHPSeed\Core\Response\ResultCode;
use PHPSeed\Core\Response\ResultGenerator;

/**
 * 初始化全局配置
 */
function bootApp()
{
    registerExceptionHandler();
    // 新增一个 IoC 容器，通过依赖注入获取对象实例
    $container = Container::getInstance();
    // restful api 用不上 session
    //initSession($container);
    initRequest($container);
    return $container;
}

// 初始化全局配置
function initConfig(Container $container)
{
    $configs = require __DIR__ . '/config.php';
    foreach ($configs as $module => $config) {
        foreach ($config as $key => $val) {
            $container->bind($module . '.' . $key, $val);
        }
    }
}

function initSession($container)
{
    $session = new Session();
    $session->start();
    $container->bind('session', $session);
}

function initRequest($container)
{
    // 捕获全局请求
    $request = Request::capture();
    $container->bind('request', $request);

    // 注册路由
    $router = require_once __DIR__ . '/routes.php';
    // 路由分发、处理请求、返回响应
    $router->dispatch($request);
}

// 注册全局异常处理器
function registerExceptionHandler()
{
    set_exception_handler(function (Throwable $exception) {
        $response = new Response();
        if ($exception instanceof BaseException) {
            $response->setContent(ResultGenerator::error($exception->getResultCode(), $exception->getMessage()));
        } else {
            $response->setContent(ResultGenerator::error(ResultCode::UNKNOWN_FAILED, $exception->getMessage()));
        }
        $response->send();
    });
}

// 启动应用
$container = bootApp();
