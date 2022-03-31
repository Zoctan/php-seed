<?php

namespace App\Core\Exception;

use App\Core\BaseException;
use App\Core\Response\ResultCode;

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
