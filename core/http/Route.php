<?php

namespace App\Core\Http;

class Route
{
    /**
     * request method list
     * 
     * @var array
     */
    public $methods;

    /**
     * request uri
     * 
     * @var string
     */
    public $uri;

    /**
     * controller handle method callback function or string
     * callback function | 'XXController@Method'
     * 
     * @var callback|string
     */
    public $action;

    /**
     * requires auth
     * 
     * @var boolean
     */
    public $auth = true;

    /**
     * permission list
     * and: ['article:add', 'article:delete'] or [ 'joint' => 'and', 'list' => ['article:add', 'article:delete'] ]
     * or:  [ 'joint' => 'or', 'list' => ['article:add', 'article:delete'] ]
     * 
     * @var array
     */
    public $permission = [];

    /**
     * response type
     * 
     * @var string
     */
    public $responseType = Response::RESPONSE_TYPE_JSON;

    public function __construct($methods, $uri, $action)
    {
        $this->methods = $methods;
        $this->uri = $uri;
        $this->action = $action;
    }

    public function setAuth(bool $auth = true)
    {
        $this->auth = $auth;
        return $this;
    }

    public function setPermission(array $permission = [])
    {
        $this->permission = $permission;
        return $this;
    }

    public function setResponseType(array $responseType = Response::RESPONSE_TYPE_JSON)
    {
        $this->responseType = $responseType;
        return $this;
    }
}
