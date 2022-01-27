<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Http\Request;
use App\Core\Http\Response;

/**
 * 控制器基类
 */
abstract class BaseController
{
    /**
     * 注入请求
     * 
     * @var Request
     */
    protected $request;

    /**
     * 注入响应
     * 
     * @var Response
     */
    protected $response;

    /**
     * 注入已认证成员
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
