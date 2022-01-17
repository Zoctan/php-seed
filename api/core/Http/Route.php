<?php

namespace Seed\Core\Http;

class Route
{
    public $methods;
    public $uri;
    public $action;
    public $params;

    public function __construct($methods, $uri, $action)
    {
        $this->methods = $methods;
        $this->uri = $uri;
        $this->action = $action;
    }
}
