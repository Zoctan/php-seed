<?php

require_once __DIR__ . "/vendor/autoload.php";

use PHPSeed\Core\DI;
use PHPSeed\Core\Http\Session;
use PHPSeed\Core\Http\Request;
use PHPSeed\Core\Http\Response;
use PHPSeed\Core\BaseException;
use PHPSeed\Core\Response\ResultCode;
use PHPSeed\Core\Response\ResultGenerator;

/**
 * 启动应用
 */
function bootApp()
{
    registerExceptionHandler();

    $di = DI::getInstance();

    // 初始化配置
    $di->config = require_once __DIR__ . "/config.php";

    // 捕获全局请求
    $di->request = Request::capture();

    // 注册响应
    $di->response = new Response();
    return $di;
}

// restful api 用不上 session
function initSession($di)
{
    $session = new Session();
    $session->start();
    $di->session = $session;
}

// 注册全局异常处理器
function registerExceptionHandler()
{
    set_exception_handler(function (Throwable $exception) {
        $response = new Response();
        $message = $exception->getMessage();
        if ($exception instanceof BaseException) {
            // 继承自基类的异常
            $message = implode("：", [$exception->getResultCode()[1], $message]);
            $response->setContent(ResultGenerator::error($exception->getResultCode(), $message));
        } else {
            // 其他异常
            $message = implode("：", [ResultCode::UNKNOWN_FAILED[1], $message]);
            $response->setContent(ResultGenerator::error(ResultCode::UNKNOWN_FAILED, $message));
        }
        $response->send();
    });
}

// 启动应用
$di = bootApp();

// 注册路由
$router = require_once __DIR__ . "/routes.php";
// 路由分发、处理请求、返回响应
$router->dispatch($di->request);
