<?php

namespace PHPSeed\Core;

use PHPSeed\Core\Http\Request;

class BaseController
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Request
     */
    protected $request;

    public function __construct()
    {
        $this->container = Container::getInstance();
        $this->request = $this->container->resolve('request');
    }
}
