<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Http\Request;
use App\Core\Http\Response;

/**
 * Base controller
 */
abstract class BaseController
{
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

    /**
     * inject authentication member
     * 
     * @var AuthMember
     */
    protected $authMember;

    public function __construct()
    {
        $this->request = \App\DI()->request;
        $this->response = \App\DI()->response;
        $this->authMember = \App\DI()->authMember;
    }
}
