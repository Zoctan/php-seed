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
     * function | 'XXController@Method'
     * 
     * @var callback|string
     */
    public $action;

    /**
     * 需要权限
     * 
     * @var boolean
     */
    public $requiresAuth = true;

    /**
     * 需要的操作权限
     * 
     * @var array
     */
    public $auth = [];

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

    public function notRequiresAuth()
    {
        $this->requiresAuth = false;
        return $this;
    }

    public function setAuth(array $auth)
    {
        $this->auth = $auth;
        return $this;
    }
}
