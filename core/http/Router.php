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
    public function register($methods, string $uri, $callback)
    {
        if (isset($this->routes[$uri])) {
            return;
        }

        // 'GET' => ['GET']
        if (is_string($methods)) {
            $methods = [$methods];
        }

        // ['GET','POST'] => ['get','post']
        foreach ($methods as $key => $value) {
            $methods[$key] = strtolower($value);
        }

        // /member/login => member/login
        $uri = !str_starts_with($uri, '/') ? $uri : substr($uri, strlen('/'));

        $route = new Route($methods, $uri, $callback);

        $this->routes[$uri] = $route;

        return $route;
    }

    /**
     * 分配请求
     */
    public function dispatch(Request $request)
    {
        $uri = $request->getPath();

        // fixme暂时这样处理upload接口
        if (strpos($uri, 'upload') === 0 && (!(strpos($uri, 'upload/add') === 0) && !(strpos($uri, 'upload/delete') === 0))) {
            $route = $this->routes['upload'];
        } else {
            if (!isset($this->routes[$uri])) {
                throw new RouterException('未知路由错误');
            }

            $route = $this->routes[$uri];
            if (!in_array(strtolower($request->getMethod()), $route->methods)) {
                throw new RouterException('路由请求方法错误');
            }
        }

        $callback = $route->action;
        if (is_callable($callback)) {
            // 通过匿名函数注册的路由回调
            // 比如：$router->register('get', '/', function () use ($container,  $request) { xxx });
            call_user_func($callback, $request);
        } elseif (is_string($callback) && strpos($callback, '@') !== FALSE) {
            // 通过控制器方法注册的路由回调
            list($controllerClass, $controllerMethod) = explode('@', $callback);
            $controllerNamespace = \App\DI()->config['app']['controllerNamespace'];
            // App\Controller\XXController
            $controllerClass = $controllerNamespace . $controllerClass;
            // 创建控制器实例
            $controllerInstance = \App\DI()->newInstance($controllerClass);
            // 调用控制器方法
            $controllerInstance->$controllerMethod();
        } else {
            throw new RouterException('路由内部回调书写错误，请联系管理员处理');
        }
    }
}
