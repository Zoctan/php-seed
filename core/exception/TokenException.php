<?php

namespace App\Core\Exception;

use App\Core\BaseException;
use App\Core\Response\ResultCode;

/**
 * Token exception
 */
class TokenException extends BaseException
{

    public function __construct($msg = '')
    {
        parent::__construct(ResultCode::TOKEN_EXCEPTION, $msg);
    }
}
