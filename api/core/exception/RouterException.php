<?php

namespace PHPSeed\Core\Exception;

use PHPSeed\Core\BaseException;
use PHPSeed\Core\Response\ResultCode;

/**
 * 路由异常
 */
class RouterException extends BaseException
{

    public function __construct($msg = "")
    {
        parent::__construct(ResultCode::ROUTER_EXCEPTION, $msg);
    }
}
