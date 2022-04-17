<?php

namespace App\Core\Exception;

use App\Core\BaseException;
use App\Core\Result\Result;
use App\Core\Result\ResultCode;

/**
 * Global exception handler
 */
class ExceptionHandler
{
    private static $showFileLine = true;

    public static function register()
    {
        self::setFatalErrorHandler();
        self::setErrorHandler();
        self::setExceptionHandler();
    }

    /**
     * Handle fatal error
     */
    private static function setFatalErrorHandler()
    {
        register_shutdown_function(function () {
            $error = error_get_last();
            if ($error !== NULL) {
                $resultCode = ResultCode::FAILED;
                // todo test
                $errno   = $error['type'];
                $errfile = $error['file'];
                $errline = $error['line'];
                $errstr  = $error['message'];
                // Fatal error, E_ERROR === 1
                if ($error['type'] === E_ERROR) {
                    $msg = sprintf('%s => %s', $resultCode[1], $errstr);
                }
                Result::error($msg, $resultCode);
            }
        });
    }

    /**
     * Handle custom throw exception
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
                $msg = sprintf('%s => %s[%s] => %s', $resultCode[1], $exception->getFile(), $exception->getLine(), $exception->getMessage());
            } else {
                $msg = sprintf('%s => %s', $resultCode[1], $exception->getMessage());
            }
            Result::error($msg, $resultCode);
        });
    }

    /**
     * Handle custom throw error
     */
    private static function setErrorHandler()
    {
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            $resultCode = ResultCode::FAILED;
            if (self::$showFileLine) {
                $msg = sprintf('%s => %s[%s] => %s', $resultCode[1], $errfile, $errline, $errstr);
            } else {
                $msg = sprintf('%s => %s', $resultCode[1], $errstr);
            }
            Result::error($msg, $resultCode);
        }, E_ALL);
    }
}
