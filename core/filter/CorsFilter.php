<?php

namespace App\Core\Filter;

use App\Core\BaseFilter;

/**
 * Cors filter
 */
class CorsFilter extends BaseFilter
{
    public function doFilter()
    {
        $this->response
            // allow all origin
            ->setHeader('Access-Control-Allow-Origin', '*')
            // allow methods
            ->setHeader('Access-Control-Allow-Methods', 'GET,POST,DELETE,PUT,PATCH,OPTIONS')
            // deny credentials
            // if frontend using axios, make sure param withCredentials = false
            ->setHeader('Access-Control-Allow-Credentials', 'false')
            // allow headers
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type,Content-Length,Authorization');

        // options request return 200 directly
        if ('OPTIONS' === $this->request->getMethod()) {
            return false;
        }
        return true;
    }
}
