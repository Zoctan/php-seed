<?php

namespace App\Core\Http;

/**
 * 路由
 */
class Route
{
    /**
     * 请求方法
     * 
     * @var array
     */
    public $methods;

    /**
     * 网址
     * 
     * @var string
     */
    public $uri;

    /**
     * 处理方法
     * function | "XXController@Method"
     * 
     * @var callback|string
     */
    public $action;

    /**
     * 需要权限
     * 
     * @var boolean
     */
    public $requiresAuth = false;

    /**
     * 需要的操作权限
     * 
     * @var array
     */
    public $authOperate = [];

    /**
     * 返回的响应类型
     * 
     * @var string
     */
    public $responseContentType = "json";

    public function __construct($methods, $uri, $action)
    {
        $this->methods = $methods;
        $this->uri = $uri;
        $this->action = $action;
    }

    public function requiresAuth()
    {
        $this->requiresAuth = true;
        return $this;
    }

    public function setAuthOperate(array $authOperate)
    {
        $this->authOperate = $authOperate;
        return $this;
    }

    public function setResponseContentType(string $responseContentType)
    {
        $this->responseContentType = $responseContentType;
        return $this;
    }
}
