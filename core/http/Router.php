<?php

namespace App\Core\Http;

use App\Core\Exception\RouterException;

/**
 * 路由器
 */
class Router
{
    protected $groupUri = '';
    protected $groupCallback = null;
    // todo 树结构
    protected $routes = [];

    /**
     * 获取路由列表
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    public function addGroup(string $groupUri = '', $groupCallback = null)
    {
        $this->groupUri = $groupUri;
        $this->groupCallback = $groupCallback;
        return $this;
    }

    /**
     * add route
     * 
     * @param array|string $methods ['GET', 'POST'] or just 'GET'
     * @param string $uri 'member/list' or '/member/list'
     * @param callback|string $callback 'MemberController@list' or function() {}
     * @param array $extra extra data for Route()
     */
    public function addRoute($methods, string $uri, $callback, array $extra = [])
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

        // eg. member/login => /member/login
        $uri = strpos($uri, '/') === false ? '/' . $uri : $uri;
        // /groupUri/...
        $uri = $this->groupUri !== '' ? $this->groupUri . $uri : $uri;

        if ($this->groupCallback) {
            if (!is_string($callback)) {
                throw new RouterException('If use $groupCallback, please make sure $callback to be a string, like: "list", "get", "search" ...');
            } else {
                if (is_string($this->groupCallback)) {
                    // eg.
                    // groupCallback => 'MemberController'
                    // callback => 'MemberController@list'
                    $callback = implode('@', [$this->groupCallback, $callback]);
                } else {
                    // eg.
                    // groupCallback => new MemberController()
                    // callback => (new MemberController())->list
                    $callback = $this->groupCallback->$callback;
                }
            }
        }

        $route = new Route($methods, $uri, $callback);

        if (!empty($extra)) {
            if (isset($extra['auth'])) {
                $route->setAuth($extra['auth']);
            }
            if (isset($extra['permission'])) {
                $route->setPermission($extra['permission']);
            }
        }

        $this->routes[$uri] = $route;

        return $this;
    }

    public function loadCache($filePath)
    {
        $file = fopen($filePath, 'r');
        $this->routes = unserialize(fread($file, filesize($filePath)));
        fclose($filePath);
    }

    public function cache($filePath)
    {
        if (fopen($filePath, 'w+') !== false) {
            file_put_contents($filePath, serialize($this->routes));
        }
        return $this;
    }

    /**
     * 分配请求
     */
    public function dispatch(Request $request)
    {
        $uri = $request->uri;

        // fixme暂时这样处理upload接口
        if (strpos($uri, '/upload/') === 0 && (strpos($uri, '/upload/add') === false && strpos($uri, '/upload/delete') === false)) {
            $route = $this->routes['/upload/'];
        } else {
            if (!isset($this->routes[$uri])) {
                throw new RouterException('unknown router');
            }

            $route = $this->routes[$uri];
            if (!in_array(strtolower($request->getMethod()), $route->methods)) {
                throw new RouterException('router method error');
            }
        }

        $callback = $route->action;
        if (is_callable($callback)) {
            // 通过匿名函数注册的路由回调
            // 比如：$router->register('get', '/', function () { xxx });
            call_user_func($callback);
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
            throw new RouterException('router inside error, please contract with administrator');
        }
    }
}
