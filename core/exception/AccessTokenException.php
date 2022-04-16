<?php

namespace App\Core\Exception;

use App\Core\BaseException;
use App\Core\Result\ResultCode;

/**
 * AccessTokenException
 */
class AccessTokenException extends BaseException
{

    public function __construct($msg = '')
    {
        parent::__construct(ResultCode::ACCESS_TOKEN_EXCEPTION, $msg);
    }
}
