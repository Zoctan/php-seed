<?php

namespace App\Core\Exception;

use App\Core\BaseException;
use App\Core\Response\ResultCode;

/**
 * 路由异常
 */
class RouterException extends BaseException
{

    public function __construct($msg = '')
    {
        parent::__construct(ResultCode::ROUTER_EXCEPTION, $msg);
    }
}