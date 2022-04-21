<?php

namespace App\Core\Router;

use App\Core\BaseController;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Exception\RouterException;

/**
 * Router
 */
class Router
{
    protected $groupUri = '';
    protected $groupCallback = null;
    protected $routeList = [];

    public function getRouteList()
    {
        return $this->routeList;
    }

    public function getRoute($uri)
    {
        return $this->routeList[$uri];
    }

    /**
     * Add group
     * 
     * @param string $groupUri
     * @param callback|string $groupCallback
     */
    public function addGroup(string $groupUri = '', $groupCallback = null)
    {
        $this->groupUri = $groupUri;
        $this->groupCallback = $groupCallback;
        return $this;
    }

    /**
     * Add route
     * 
     * @param array|string $methods ['GET', 'POST'] or just 'GET'
     * @param string $uri 'member/list' or '/member/list'
     * @param class|instance|callback|string $callback 'MemberController@list' or closure function() {}
     * @param array $extra extra data for Route()
     */
    public function addRoute($methods, string $uri, $callback, array $extra = [])
    {
        // eg. member/login => /member/login
        $uri = strpos($uri, '/') === false ? '/' . $uri : $uri;
        // /$groupUri/$uri
        $uri = $this->groupUri !== '' ? $this->groupUri . $uri : $uri;

        if (isset($this->routeList[$uri])) {
            throw new RouterException('Url repeat: ' . $uri);
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
                    if (!class_exists($this->groupCallback)) {
                        throw new RouterException("$this->groupCallback class does not exist");
                    }
                    // eg.
                    // groupCallback => 'DemoController'
                    // callback => 'DemoController@list'
                    $callback = implode('@', [$this->groupCallback, $callback]);
                } else {
                    $controllerInstance = $this->groupCallback;
                    // DemoController::class
                    if (!($controllerInstance instanceof BaseController)) {
                        // new class instance
                        $controllerInstance = \App\DI()->newInstance($controllerInstance);
                    }
                    // eg.
                    // groupCallback => new DemoController()
                    // callback => (new DemoController())->list
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
            if (isset($extra['mimeType'])) {
                $route->setMimeType($extra['mimeType']);
            }
        }

        $this->routeList[$uri] = $route;

        return $this;
    }

    /**
     * Load cache file
     * 
     * @param string $filePath
     */
    public function loadCache($filePath)
    {
        if (empty($filePath)) {
            throw new RouterException('FilePath does not exist');
        }
        $file = fopen($filePath, 'r');
        $this->routeList = unserialize(fread($file, filesize($filePath)));
        fclose($filePath);
    }

    /**
     * Do not cache when using closure function as addRoute() callback param
     * 
     * @param string $filePath
     */
    public function cache($filePath)
    {
        if (empty($this->routeList)) {
            throw new RouterException('Route list empty, please add some routes first');
        }

        if (!file_put_contents($filePath, serialize($this->routeList))) {
            throw new RouterException('Cache routes error');
        }
        return $this;
    }

    /**
     * Dispatch request and response it
     * 
     * @param Request $request
     * @param Response $response
     */
    public function dispatch(Request $request, Response $response)
    {
        if (empty($this->getRouteList())) {
            throw new RouterException('Route list empty, please add some routes first');
        }

        $uri = $request->uri;
        $method = $request->method;
        $route = $this->getRoute($uri);

        if ($route === null) {
            throw new RouterException('Unknown route');
        }

        if (!in_array($method, $route->methods)) {
            throw new RouterException('Route method error');
        }

        $callback = $route->action;
        $response->setMimeType($route->mimeType);
        if (is_callable($callback)) {
            // call closure function, like: $router->register('get', '/', function () { ... });
            call_user_func($callback);
        } elseif (is_string($callback) && strpos($callback, '@') !== false) {
            // like: $router->register('get', '/', 'DemoController@list'); 
            list($controllerClass, $controllerMethod) = explode('@', $callback);
            // new class instance
            $controllerInstance = \App\DI()->newInstance($controllerClass);
            // call method
            $controllerInstance->$controllerMethod();
        } else {
            throw new RouterException('Route inside error, please contract with administrator');
        }
    }
}
