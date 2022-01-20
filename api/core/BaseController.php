<?php

namespace PHPSeed\Core;

use PHPSeed\Core\Http\Request;
use PHPSeed\Core\Http\Response;

/**
 * 控制器基类
 */
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

    /**
     * @var Response
     */
    protected $response;

    public function __construct()
    {
        $this->di = DI::getInstance();
        $this->request = $this->di->request;
        $this->response = $this->di->response;
    }
}
