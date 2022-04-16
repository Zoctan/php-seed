<?php

namespace App\Core\Exception;

use App\Core\BaseException;
use App\Core\Result\ResultCode;

/**
 * RefreshTokenException
 */
class RefreshTokenException extends BaseException
{

    public function __construct($msg = '')
    {
        parent::__construct(ResultCode::REFRESH_TOKEN_EXCEPTION, $msg);
    }
}
