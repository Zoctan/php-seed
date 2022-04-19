<?php

namespace App\Core\Http;

use App\Core\Response\MimeType;

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
     * and: ['article:add', 'article:remove'] or [ 'joint' => 'and', 'list' => ['article:add', 'article:remove'] ]
     * or:  [ 'joint' => 'or', 'list' => ['article:add', 'article:remove'] ]
     * 
     * @var array
     */
    public $permission = [];

    /**
     * mimeType
     * 
     * @var string
     */
    public $mimeType = MimeType::JSON;

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

    public function setMimeType(array $mimeType = MimeType::JSON)
    {
        $this->mimeType = $mimeType;
        return $this;
    }
}
