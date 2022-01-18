<?php

namespace PHPSeed\Core\Exception;

use PHPSeed\Core\BaseException;
use PHPSeed\Core\Response\ResultCode;

/**
 * 数据库异常
 */
class DatabaseException extends BaseException
{

    public function __construct($msg = "")
    {
        parent::__construct(ResultCode::DATABASE_EXCEPTION, $msg);
    }
}
