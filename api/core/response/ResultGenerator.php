<?php

namespace PHPSeed\Core\Response;

/**
 * 响应结果生成工具
 */
class ResultGenerator
{

    /**
     * 成功响应结果（重载）
     * 
     * @return string 响应结果
     */
    public static function success()
    {
        // 获得参数数量
        $argsNum = func_num_args();
        // 获得参数数组
        $args = func_get_args();
        switch ($argsNum) {
            case 1:
                return self::successWithMsg($args[0]);
            case 2:
                return self::successWithMsgData($args[0], $args[1]);
            case 0:
            default:
                return (new Result())
                    ->setErrno(ResultCode::SUCCEED[0])
                    ->setMsg(ResultCode::SUCCEED[1])
                    ->response();
        }
    }

    /**
     * 成功响应结果
     *
     * @param $msg 消息
     * @return string 响应结果
     */
    public static function successWithMsg($msg = "")
    {
        return (new Result())
            ->setErrno(ResultCode::SUCCEED[0])
            ->setMsg($msg)
            ->response();
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

    /**
     * 失败响应结果（重载）
     * 
     * @return string 响应结果
     */
    public static function error()
    {
        $argsNum = func_num_args();
        $args = func_get_args();
        switch ($argsNum) {
            case 1:
                return self::errorWithCode($args[0]);
            case 2:
                return self::errorWithCodeMsg($args[0], $args[1]);
            case 3:
                return self::errorWithCodeMsgData($args[0], $args[1], $args[2]);
            case 0:
            default:
                return (new Result())
                    ->setErrno(ResultCode::FAILED[0])
                    ->setMsg(ResultCode::FAILED[1])
                    ->response();
        }
    }

    /**
     * 失败响应结果
     *
     * @param $resultCode 状态码枚举
     * @return string 响应结果
     */
    public static function errorWithCode(array $resultCode)
    {
        return (new Result())
            ->setErrno($resultCode[0])
            ->setMsg($resultCode[1])
            ->response();
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
        return (new Result())
            ->setErrno($resultCode[0])
            ->setMsg(!empty($msg) ? $msg : $resultCode[1])
            ->response();
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
