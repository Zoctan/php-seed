<?php

namespace App\Core\Response;

/**
 * 响应状态码枚举类
 * 
 * 业务错误 2*** 开始
 * 类异常 4*** 开始
 */
class ResultCode
{
    /** 成功请求 */
    public const SUCCEED = [0, 'success'];

    /** 失败请求 */
    public const FAILED = [1, 'failed'];

    /** 未知异常 */
    public const UNKNOWN_FAILED = [2, 'unknown failed'];

    /** 成功请求，但结果不是期望的成功结果 */
    public const SUCCEED_REQUEST_FAILED_RESULT = [1000, 'succeed request, but failed result'];

    /** 查询失败 */
    public const FIND_FAILED = [2000, 'find failed'];

    /** 保存失败 */
    public const SAVE_FAILED = [2001, 'save failed'];

    /** 更新失败 */
    public const UPDATE_FAILED = [2002, 'update failed'];

    /** 删除失败 */
    public const DELETE_FAILED = [2003, 'delete failed'];

    /** 账户名重复 */
    public const DUPLICATE_NAME = [2004, 'duplicate name'];

    /** 凭证异常 */
    public const TOKEN_EXCEPTION = [4001, 'token exception'];

    /** 通行凭证异常 */
    public const ACCESS_TOKEN_EXCEPTION = [4002, 'access token exception'];

    /** 刷新凭证异常 */
    public const REFRESH_TOKEN_EXCEPTION = [4003, 'refresh token exception'];

    /** 数据库异常 */
    public const DATABASE_EXCEPTION = [4004, 'datebase exception'];

    /** 验证异常 */
    public const VIOLATION_EXCEPTION = [4005, 'violation exception'];

    /** 路由异常 */
    public const ROUTER_EXCEPTION = [4006, 'router exception'];
}
