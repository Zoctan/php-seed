<?php

namespace App\Core;

/**
 * 异常基类
 */
class BaseException extends \Exception
{
    private $resultCode;

    public function __construct(array $resultCode, $msg = "")
    {
        parent::__construct($msg);
        $this->resultCode = $resultCode;
    }

    public function getResultCode()
    {
        return $this->resultCode;
    }
}
