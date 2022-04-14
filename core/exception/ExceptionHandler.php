<?php

namespace App\Core\Exception;

use App\Core\BaseException;
use App\Core\Response\ResultGenerator;
use App\Core\Response\ResultCode;

/**
 * Global exception handler
 */
class ExceptionHandler
{
    private static $showFileLine = true;

    public static function register()
    {
        self::setErrorHandler();
        self::setExceptionHandler();
    }

    /**
     * handle custom throw exception
     */
    private static function setExceptionHandler()
    {
        set_exception_handler(function (\Throwable $exception) {
            $resultCode = [];
            if ($exception instanceof BaseException) {
                // extend from BaseException
                $resultCode = $exception->getResultCode();
            } else {
                // other Exception
                $resultCode = ResultCode::UNKNOWN_FAILED;
            }
            if (self::$showFileLine) {
                $message = sprintf(
                    '%s => %s[%s] => %s',
                    $resultCode[1],
                    $exception->getFile(),
                    $exception->getLine(),
                    $exception->getMessage()
                );
            } else {
                $message = sprintf(
                    '%s => %s',
                    $resultCode[1],
                    $exception->getMessage()
                );
            }
            ResultGenerator::errorWithCodeMsg($resultCode, $message);
        });
    }

    /**
     * handle custom throw error
     */
    private static function setErrorHandler()
    {
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            $resultCode = ResultCode::FAILED;
            if (self::$showFileLine) {
                $message = sprintf(
                    '%s => %s[%s] => %s',
                    $resultCode[1],
                    $errfile,
                    $errline,
                    $errstr
                );
            } else {
                $message = sprintf(
                    '%s => %s',
                    $resultCode[1],
                    $errstr
                );
            }
            ResultGenerator::errorWithCodeMsg($resultCode, $message);
        }, E_ALL);
    }
}
