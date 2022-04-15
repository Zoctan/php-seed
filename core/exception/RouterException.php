<?php

namespace App\Core\Exception;

use App\Core\BaseException;
use App\Core\Response\ResultCode;

/**
 * RouterException
 */
class RouterException extends BaseException
{

    public function __construct($msg = '')
    {
        parent::__construct(ResultCode::ROUTER_EXCEPTION, $msg);
    }
}
