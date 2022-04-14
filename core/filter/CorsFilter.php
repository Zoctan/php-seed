<?php

namespace App\Core\Filter;

use App\Core\Filter;
use App\Core\Http\Response;

/**
 * Cors filter
 */
class CorsFilter implements Filter
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var Response
     */
    private $response;

    public function __construct()
    {
        $this->request = \App\DI()->request;
        $this->response = \App\DI()->response;
    }

    public function doFilter()
    {
        $this->response
            // allow all origin
            ->appendHeader('Access-Control-Allow-Origin', '*')
            // allow methods
            ->appendHeader('Access-Control-Allow-Methods', 'GET,POST,DELETE,PUT,PATCH,OPTIONS')
            // deny credentials
            // if frontend using axios, make sure param withCredentials = false
            ->appendHeader('Access-Control-Allow-Credentials', 'false')
            // allow headers
            ->appendHeader('Access-Control-Allow-Headers', 'Content-Type,Content-Length,Authorization');

        // options request return 200 directly
        if ('OPTIONS' === $this->request->getMethod()) {
            return false;
        }
        return true;
    }
}
