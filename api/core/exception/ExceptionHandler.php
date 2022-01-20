<?php

namespace PHPSeed\Core\Exception;

use Throwable;
use PHPSeed\Core\BaseException;
use PHPSeed\Core\Response\ResultGenerator;
use PHPSeed\Core\Response\ResultCode;

/**
 * 全局异常处理器
 */
class ExceptionHandler
{
    public function __construct()
    {
        set_exception_handler(function (Throwable $exception) {
            $message = $exception->getMessage();
            if ($exception instanceof BaseException) {
                // 继承自基类的异常
                $message = implode(": ", [$exception->getResultCode()[1], $message]);
                ResultGenerator::error($exception->getResultCode(), $message);
            } else {
                // 其他异常
                $message = implode(": ", [ResultCode::UNKNOWN_FAILED[1], $message]);
                ResultGenerator::error(ResultCode::UNKNOWN_FAILED, $message);
            }
        });
    }
}
