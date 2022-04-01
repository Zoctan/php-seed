<?php

namespace App\Core\Filter;

use App\Core\Filter;
use App\Core\Http\Response;

/**
 * 跨域过滤器
 */
class CorsFilter implements Filter
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var Response
     */
    private $response;

    public function __construct()
    {
        $this->request = \App\DI()->request;
        $this->response = \App\DI()->response;
    }

    public function doFilter()
    {
        $this->response
            // 允许任何网址请求
            ->appendHeader('Access-Control-Allow-Origin', '*')
            // 允许请求的类型
            ->appendHeader('Access-Control-Allow-Methods', 'GET,POST,DELETE,PUT,PATCH,OPTIONS')
            // 不允许带 cookies
            // 若请求框架是 axios，要关闭 withCredentials: false
            ->appendHeader('Access-Control-Allow-Credentials', 'false')
            // 设置允许自定义请求头的字段
            ->appendHeader('Access-Control-Allow-Headers', 'Content-Type,Content-Length,Authorization');

        // 预请求后，直接返回
        if ('OPTIONS' === $this->request->getMethod()) {
            return false;
        }
        return true;
    }
}
