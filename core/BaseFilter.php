<?php

namespace App\Core;

/**
 * Customize base filter class
 */
class BaseFilter
{
    /**
     * inject global router
     * 
     * @var Router
     */
    protected $router;

    /**
     * inject global request
     * 
     * @var Request
     */
    protected $request;

    /**
     * inject global response
     * 
     * @var Response
     */
    protected $response;

    public function __construct()
    {
        $this->router = \App\DI()->router;
        $this->request = \App\DI()->request;
        $this->response = \App\DI()->response;
    }
    
    /**
     * Do what filter define
     */
    public function doFilter(){}
}
