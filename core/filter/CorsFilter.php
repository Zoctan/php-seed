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
     * @var array
     */
    private $routes;
    private $request;
    private $response;

    public function __construct($routes)
    {
        $this->routes = $routes;
        $this->request = \App\DI()->request;
        $this->response = \App\DI()->response;
    }

    public function doFilter()
    {
        // 根据路由设置返回的类型
        $this->setContentType();
        // 允许任何网址请求
        $this->response->headers->set("Access-Control-Allow-Origin", "*");
        // 允许请求的类型
        $this->response->headers->set("Access-Control-Allow-Methods", "GET,POST,DELETE,PUT,PATCH,OPTIONS");
        // 不允许带 cookies
        // 若请求框架是 axios，要关闭 withCredentials: false
        $this->response->headers->set("Access-Control-Allow-Credentials", "false");
        // 设置允许自定义请求头的字段
        $this->response->headers->set("Access-Control-Allow-Headers", "Content-Type,Content-Length,Authorization");

        // 预请求后，直接返回
        if ("OPTIONS" == $this->request->getMethod()) {
            return false;
        }
        return true;
    }

    public function setContentType()
    {
        $uri = $this->request->getPath();
        // fixme暂时这样处理upload接口
        if (strpos($uri, "upload") === 0 && (!(strpos($uri, "upload/add") === 0) && !(strpos($uri, "upload/delete") === 0))) {
            return;
        }
        $responseContentType = $this->routes[$uri]->responseContentType;
        $contentType = "";
        switch ($responseContentType) {
            default:
            case "json":
                $contentType = "application/json;charset=UTF-8";
                break;
        }
        $this->response->headers->set("Content-type", $contentType);
    }
}
