<?php

namespace App\Core\Response;

/**
 * 响应结果生成工具
 */
class ResultGenerator
{
    /** ------------------ 成功响应结果 ------------------ **/

    /**
     * 成功响应结果
     * 
     * @return string 响应结果
     */
    public static function success()
    {
        return self::successWithMsgData(ResultCode::SUCCEED[1]);
    }

    /**
     * 成功响应结果
     *
     * @param $msg 消息
     * @return string 响应结果
     */
    public static function successWithMsg($msg = "")
    {
        return self::successWithMsgData($msg);
    }

    /**
     * 成功响应结果
     *
     * @param $data 数据
     * @return string 响应结果
     */
    public static function successWithData($data = "")
    {
        return self::successWithMsgData("", $data);
    }

    /**
     * 成功响应结果
     *
     * @param $msg 消息
     * @param $data 数据
     * @return string 响应结果
     */
    public static function successWithMsgData($msg = "", $data = null)
    {
        return (new Result())
            ->setErrno(ResultCode::SUCCEED[0])
            ->setMsg($msg)
            ->setData($data)
            ->response();
    }

    /** ------------------ 失败响应结果 ------------------ **/

    /**
     * 失败响应结果
     * 
     * @return string 响应结果
     */
    public static function error()
    {
        return self::errorWithCodeMsgData(ResultCode::FAILED);
    }

    /**
     * 失败响应结果
     *
     * @param $msg 消息
     * @return string 响应结果
     */
    public static function errorWithMsg($msg = "")
    {
        return self::errorWithCodeMsgData(ResultCode::FAILED, $msg);
    }

    /**
     * 失败响应结果
     *
     * @param $resultCode 状态码枚举
     * @return string 响应结果
     */
    public static function errorWithCode(array $resultCode)
    {
        return self::errorWithCodeMsgData($resultCode);
    }

    /**
     * 失败响应结果
     *
     * @param $resultCode 状态码枚举
     * @param $msg 消息
     * @return string 响应结果
     */
    public static function errorWithCodeMsg(array $resultCode, $msg = "")
    {
        return self::errorWithCodeMsgData($resultCode, $msg);
    }

    /**
     * 失败响应结果
     *
     * @param $resultCode 状态码枚举
     * @param $msg 消息
     * @param $data 数据
     * @return string 响应结果
     */
    public static function errorWithCodeMsgData(array $resultCode, $msg = "", $data = null)
    {
        return (new Result())
            ->setErrno($resultCode[0])
            ->setMsg(!empty($msg) ? $msg : $resultCode[1])
            ->setData($data)
            ->response();
    }
}
