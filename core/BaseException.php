<?php

namespace App\Core;

/**
 * Customize base exception class
 */
class BaseException extends \Exception
{
    private $resultCode;

    public function __construct(array $resultCode, $msg = '')
    {
        parent::__construct($msg);
        $this->resultCode = $resultCode;
    }

    public function getResultCode()
    {
        return $this->resultCode;
    }
}
