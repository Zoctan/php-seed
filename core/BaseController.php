<?php

namespace App\Core;

use App\Core\Http\Request;
use App\Core\Http\Response;

/**
 * Customize base controller class
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
