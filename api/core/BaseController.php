<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Http\Request;
use App\Core\Http\Response;

/**
 * 控制器基类
 */
class BaseController
{
    /**
     * @var DependencyInjection
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
        $this->di = \App\DI();
        $this->request = $this->di->request;
        $this->response = $this->di->response;
    }
}
