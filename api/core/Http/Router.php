<?php

namespace Seed\Core\Http;

use Exception;

class Router
{
    protected $routes = [];

    public function register($methods, $uri, $callback)
    {
        if (isset($this->routes[$uri])) {
            return;
        }
        if (is_string($methods)) {
            $methods = [$methods];
        }
        $route = new Route($methods, $uri, $callback);
        $this->routes[$uri] = $route;
    }

    public function dispatch(Request $request)
    {
        $path = $request->getPath();
        if (!isset($this->routes[$path])) {
            // 未定义路由重定向到首页
            $response = new Response('', 301, ['Location' => '/']);
            $response->prepare($request)->send();
            exit();
        }
        $route = $this->routes[$path];
        if (!in_array(strtolower($request->getMethod()), $route->methods)) {
            throw new Exception('HTTP 请求方法不正确');
        }
        $callback = $route->action;
        if (is_callable($callback)) {
            call_user_func($callback, $request);
        } elseif (is_string($callback) && strpos($callback, '@') !== FALSE) {
            // @todo 为控制器方法路由预留
        } else {
            throw new Exception('无效的路由回调');
        }
    }
}
