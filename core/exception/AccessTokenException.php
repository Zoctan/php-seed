<?php

namespace App\Core\Exception;

use App\Core\BaseException;
use App\Core\Response\ResultCode;

/**
 * Access token exception
 */
class AccessTokenException extends BaseException
{

    public function __construct($msg = '')
    {
        parent::__construct(ResultCode::ACCESS_TOKEN_EXCEPTION, $msg);
    }
}
