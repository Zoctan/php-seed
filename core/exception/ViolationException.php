<?php

namespace App\Core\Exception;

use App\Core\BaseException;
use App\Core\Response\ResultCode;

/**
 * Violation exception
 */
class ViolationException extends BaseException
{

    public function __construct($msg = '')
    {
        parent::__construct(ResultCode::VIOLATION_EXCEPTION, $msg);
    }
}
