<?php

namespace PHPSeed\Core\Exception;

use PHPSeed\Core\BaseException;
use PHPSeed\Core\Response\ResultCode;

/**
 * 验证异常
 */
class ViolationException extends BaseException
{

    public function __construct($msg = "")
    {
        parent::__construct(ResultCode::VIOLATION_EXCEPTION, $msg);
    }
}
