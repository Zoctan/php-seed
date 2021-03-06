<?php

namespace App\Core\Exception;

use App\Core\BaseException;
use App\Core\Result\ResultCode;

/**
 * TokenException
 */
class TokenException extends BaseException
{

    public function __construct($msg = '')
    {
        parent::__construct(ResultCode::TOKEN_EXCEPTION, $msg);
    }
}
