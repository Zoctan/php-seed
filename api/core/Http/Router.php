<?php

namespace App\Core\Http;

use App\Core\Exception\RouterException;

/**
 * 路由器
 */
class Router
{

    /**
     * 路由列表
     */
    protected $routes = [];

    /**
     * 获取路由列表
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * 注册路由
     */
    public function register($methods, $uri, $callback, $needAuth = false)
    {
        if (isset($this->routes[$uri])) {
            return;
        }
        if (is_string($methods)) {
            $methods = [$methods];
        }
        $route = new Route($methods, $uri, $callback, $needAuth);
        $this->routes[$uri] = $route;
    }

    /**
     * 分配请求
     */
    public function dispatch(Request $request)
    {
        $path = $request->getPath();
        if (!isset($this->routes[$path])) {
            throw new RouterException("未知路由错误");
        }

        $route = $this->routes[$path];
        if (!in_array(strtolower($request->getMethod()), $route->methods)) {
            throw new RouterException("路由请求方法错误");
        }

        $callback = $route->action;
        if (is_callable($callback)) {
            // 通过匿名函数注册的路由回调
            // 比如：$router->register("get", "/", function () use ($container,  $request) { xxx });
            call_user_func($callback, $request);
        } elseif (is_string($callback) && strpos($callback, "@") !== FALSE) {
            // 通过控制器方法注册的路由回调
            list($controllerClass, $controllerMethod) = explode("@", $callback);
            $controllerNamespace = \App\DI()->config->app->controllerNamespace;
            $controllerClass = $controllerNamespace . $controllerClass;
            $controllerInstance = new $controllerClass;
            call_user_func([$controllerInstance, $controllerMethod]);
        } else {
            throw new RouterException("路由内部回调书写错误，请联系管理员处理");
        }
    }
}
