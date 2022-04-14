<?php

namespace App\Core\Response;

/**
 * Result generator
 */
class ResultGenerator
{
    /** ------------------ success result ------------------ **/

    /**
     * success result
     * 
     * @return string
     */
    public static function success()
    {
        return self::successWithMsgData(ResultCode::SUCCEED[1]);
    }

    /**
     * success result
     *
     * @param $msg message
     * @return string
     */
    public static function successWithMsg($msg = '')
    {
        return self::successWithMsgData($msg);
    }

    /**
     * success result
     *
     * @param $data data
     * @return string
     */
    public static function successWithData($data = '')
    {
        return self::successWithMsgData('', $data);
    }

    /**
     * success result
     *
     * @param $msg message
     * @param $data data
     * @return string
     */
    public static function successWithMsgData($msg = '', $data = null)
    {
        return (new Result())
            ->setErrno(ResultCode::SUCCEED[0])
            ->setMsg($msg)
            ->setData($data)
            ->response();
    }

    /** ------------------ error result ------------------ **/

    /**
     * error result
     * 
     * @return string
     */
    public static function error()
    {
        return self::errorWithCodeMsgData(ResultCode::FAILED);
    }

    /**
     * error result
     *
     * @param $msg message
     * @return string
     */
    public static function errorWithMsg($msg = '')
    {
        return self::errorWithCodeMsgData(ResultCode::FAILED, $msg);
    }

    /**
     * error result
     *
     * @param array $resultCode enum result code
     * @return string
     */
    public static function errorWithCode(array $resultCode)
    {
        return self::errorWithCodeMsgData($resultCode);
    }

    /**
     * error result
     *
     * @param array $resultCode enum result code
     * @param string $msg message
     * @return string
     */
    public static function errorWithCodeMsg(array $resultCode, $msg = '')
    {
        return self::errorWithCodeMsgData($resultCode, $msg);
    }

    /**
     * error result
     *
     * @param array $resultCode enum result code
     * @param string $msg message
     * @param $data data
     * @return string
     */
    public static function errorWithCodeMsgData(array $resultCode, $msg = '', $data = null)
    {
        return (new Result())
            ->setErrno($resultCode[0])
            ->setMsg(!empty($msg) ? $msg : $resultCode[1])
            ->setData($data)
            ->response();
    }
}
