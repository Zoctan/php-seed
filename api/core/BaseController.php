<?php

namespace PHPSeed\Core;

use PHPSeed\Core\Http\Request;

class BaseController
{
    /**
     * @var DI
     */
    protected $di;

    /**
     * @var Request
     */
    protected $request;

    public function __construct()
    {
        $this->di = DI::getInstance();
        $this->request = $this->di->request;
    }
}
