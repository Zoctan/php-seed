<?php

namespace App\Core\Exception;

use App\Core\BaseException;
use App\Core\Response\ResultCode;

/**
 * Database exception
 */
class DatabaseException extends BaseException
{

    public function __construct($msg = '')
    {
        parent::__construct(ResultCode::DATABASE_EXCEPTION, $msg);
    }
}
