<?php

namespace App\Core\Http;

/**
 * 路由
 */
class Route
{
    // 请求方法
    public $methods;
    // 网址
    public $uri;
    // 处理方法
    public $action;
    // 参数
    public $params;
    // 需要认证
    public $needAuth;

    public function __construct($methods, $uri, $action, $needAuth)
    {
        $this->methods = $methods;
        $this->uri = $uri;
        $this->action = $action;
        $this->needAuth = $needAuth;
    }
}
