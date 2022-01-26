<?php

namespace App\Core\Filter;

use App\Core\Filter;
use App\Core\Http\Response;

/**
 * 跨域过滤器
 */
class CorsFilter implements Filter
{
    protected $config;

    public function __construct()
    {
        $this->config = \App\DI()->config;
    }

    public function doFilter()
    {
        $response = \App\DI()->response;
        $request = \App\DI()->request;
        // 返回的类型
        $this->setContentType($response);
        // 允许任何网址请求
        $response->headers->set("Access-Control-Allow-Origin", "*");
        // 允许请求的类型
        $response->headers->set("Access-Control-Allow-Methods", "GET,POST,DELETE,PUT,PATCH,OPTIONS");
        // 不允许带 cookies
        // 若请求框架是 axios，要关闭 withCredentials: false
        $response->headers->set("Access-Control-Allow-Credentials", "false");
        // 设置允许自定义请求头的字段
        $response->headers->set("Access-Control-Allow-Headers", "Content-Type,Content-Length,Authorization");

        // 预请求后，直接返回
        if ("OPTIONS" == $request->getMethod()) {
            return false;
        }
        return true;
    }

    public function setContentType(Response $response)
    {
        $contentType = "";
        switch ($this->config->app->response->type) {
            default:
            case "json":
                $contentType = "application/json;charset=UTF-8";
                break;
        }
        $response->headers->set("Content-type", $contentType);
    }
}
