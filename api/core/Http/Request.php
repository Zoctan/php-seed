<?php

namespace Seed\Core\Http;

use Symfony\Component\HttpFoundation\Request as BaseRequest;

class Request extends BaseRequest
{
    public static function capture()
    {
        static::enableHttpMethodParameterOverride();
        return static::createFromGlobals();
    }

    public function getPath()
    {
        $path = trim($this->getPathInfo(), '/');
        return $path == '' ? '/' : $path;
    }
}
