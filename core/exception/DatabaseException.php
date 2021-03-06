<?php

namespace App\Core\Exception;

use App\Core\BaseException;
use App\Core\Result\ResultCode;

/**
 * DatabaseException
 */
class DatabaseException extends BaseException
{

    public function __construct($msg = '')
    {
        parent::__construct(ResultCode::DATABASE_EXCEPTION, $msg);
    }
}
