<?php

namespace PHPSeed\Core\Http;

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

    public function __construct($methods, $uri, $action)
    {
        $this->methods = $methods;
        $this->uri = $uri;
        $this->action = $action;
    }
}
