<?php

namespace PHPSeed\Core\Exception;

use PHPSeed\Core\BaseException;
use PHPSeed\Core\Response\ResultCode;

/**
 * 认证异常
 */
class UnAuthorizedException extends BaseException
{

    public function __construct($msg = "")
    {
        parent::__construct(ResultCode::UNAUTHORIZED_EXCEPTION, $msg);
    }
}
