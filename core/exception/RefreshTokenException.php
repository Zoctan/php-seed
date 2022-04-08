<?php

namespace App\Core\Exception;

use App\Core\BaseException;
use App\Core\Response\ResultCode;

/**
 * 刷新凭证异常
 */
class RefreshTokenException extends BaseException
{

    public function __construct($msg = '')
    {
        parent::__construct(ResultCode::REFRESH_TOKEN_EXCEPTION, $msg);
    }
}
