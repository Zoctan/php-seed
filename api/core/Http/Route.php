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
     * 需要的操作权限
     * 
     * @var string
     */
    public $authOperate;

    public function __construct($methods, $uri, $action, string $authOperate = null)
    {
        $this->methods = $methods;
        $this->uri = $uri;
        $this->action = $action;
        $this->authOperate = $authOperate;
    }
}
