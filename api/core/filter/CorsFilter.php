<?php

namespace PHPSeed\Core\Filter;

use PHPSeed\Core\DI;
use PHPSeed\Core\Filter;

/**
 * 跨域过滤器
 */
class CorsFilter implements Filter
{
    public function doFilter()
    {
        $response = DI::getInstance()->response;
        // 允许任何网址请求
        $response->headers->set("Access-Control-Allow-Origin", "*");
        // 返回的类型
        $response->headers->set("Content-type", "application/json;charset=UTF-8");
        // 允许请求的类型
        $response->headers->set("Access-Control-Allow-Methods", "GET,POST,DELETE,PUT,PATCH,OPTIONS");
        // 不允许带 cookies
        // 请求框架是 axios，关闭 withCredentials: false
        $response->headers->set("Access-Control-Allow-Credentials", "false");
        // 设置允许自定义请求头的字段
        $response->headers->set("Access-Control-Allow-Headers", "Content-Type,Content-Length,Authorization");
    }
}
