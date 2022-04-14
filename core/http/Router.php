<?php

namespace App\Core\Http;

use App\Core\Exception\RouterException;

/**
 * Router
 */
class Router
{
    protected $groupUri = '';
    protected $groupCallback = null;

    protected $routeList = [];

    public function getrouteList()
    {
        return $this->routeList;
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
     * @param callback|string $callback 'MemberController@list' or closure function() {}
     * @param array $extra extra data for Route()
     */
    public function addRoute($methods, string $uri, $callback, array $extra = [])
    {
        // eg. member/login => /member/login
        $uri = strpos($uri, '/') === false ? '/' . $uri : $uri;
        // /$groupUri/$uri
        $uri = $this->groupUri !== '' ? $this->groupUri . $uri : $uri;

        if (isset($this->routeList[$uri])) {
            throw new RouterException('url repeat: ' . $uri);
        }

        // 'GET' => ['GET']
        // '*' => ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS']
        if (is_string($methods)) {
            if ($methods === '*') {
                $methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'];
            } else {
                $methods = [$methods];
            }
        }

        // ['get','post'] => ['GET','POST']
        foreach ($methods as $key => $value) {
            $methods[$key] = strtoupper($value);
        }

        if ($this->groupCallback) {
            if (!is_string($callback)) {
                throw new RouterException('If use $groupCallback, please make sure $callback type is a string, like: "list", "get", "search" ...');
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
            if (isset($extra['responseType'])) {
                $route->setResponseType($extra['responseType']);
            }
        }

        $this->routeList[$uri] = $route;

        return $this;
    }

    public function loadCache($filePath)
    {
        $file = fopen($filePath, 'r');
        $this->routeList = unserialize(fread($file, filesize($filePath)));
        fclose($filePath);
    }

    /**
     * do not cache when using closure function as addRoute() callback param
     */
    public function cache($filePath)
    {
        if (empty($this->routeList)) {
            throw new RouterException('route list empty, please add some routes.');
        }

        if (!file_put_contents($filePath, serialize($this->routeList))) {
            throw new RouterException('cache routes error');
        }
        return $this;
    }

    public function dispatch(Request $request, Response $response)
    {
        if (empty($this->routeList)) {
            throw new RouterException('route list empty, please add some routes.');
        }

        $uri = $request->uri;
        $method = $request->getMethod();

        if (!isset($this->routeList[$uri])) {
            throw new RouterException('unknown route');
        }

        $route = $this->routeList[$uri];
        if (!in_array($method, $route->methods)) {
            throw new RouterException('route method error');
        }

        $callback = $route->action;
        $response->setResponseType($route->responseType);
        if (is_callable($callback)) {
            // call closure function, like: $router->register('get', '/', function () { xxx });
            call_user_func($callback);
        } elseif (is_string($callback) && strpos($callback, '@') !== false) {
            // like: $router->register('get', '/', 'MemberController@list'); 
            list($controllerClass, $controllerMethod) = explode('@', $callback);
            $controllerNamespace = \App\DI()->config['app']['controllerNamespace'];
            // App\Controller\XXController
            $controllerClass = $controllerNamespace . $controllerClass;
            // new class instance
            $controllerInstance = \App\DI()->newInstance($controllerClass);
            // call method
            $controllerInstance->$controllerMethod();
        } else {
            throw new RouterException('route inside error, please contract with administrator');
        }
    }
}
