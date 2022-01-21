<?php

namespace App\Core\Exception;

use App\Core\BaseException;
use App\Core\Response\ResultGenerator;
use App\Core\Response\ResultCode;

/**
 * 全局异常处理器
 */
class ExceptionHandler
{
    private static $showFileLine = false;

    public function __construct()
    {
        set_exception_handler(function (\Throwable $exception) {
            $resultCode = [];
            if ($exception instanceof BaseException) {
                // 继承自基类的异常
                $resultCode = $exception->getResultCode();
            } else {
                // 其他异常
                $resultCode = ResultCode::UNKNOWN_FAILED;
            }
            if (self::$showFileLine) {
                $message = sprintf(
                    "%s => %s[%s] => %s",
                    $resultCode[1],
                    $exception->getFile(),
                    $exception->getLine(),
                    $exception->getMessage()
                );
            } else {
                $message = sprintf(
                    "%s => %s",
                    $resultCode[1],
                    $exception->getMessage()
                );
            }
            ResultGenerator::error($resultCode, $message);
        });
    }
}
