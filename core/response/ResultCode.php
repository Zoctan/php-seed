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
    const SUCCEED = [0, "success"];

    /** 失败请求 */
    const FAILED = [1, "failed"];

    /** 未知异常 */
    const UNKNOWN_FAILED = [2, "unknown failed"];

    /** 成功请求，但结果不是期望的成功结果 */
    const SUCCEED_REQUEST_FAILED_RESULT = [1000, "succeed request, but failed result"];

    /** 查询失败 */
    const FIND_FAILED = [2000, "find failed"];

    /** 保存失败 */
    const SAVE_FAILED = [2001, "save failed"];

    /** 更新失败 */
    const UPDATE_FAILED = [2002, "update failed"];

    /** 删除失败 */
    const DELETE_FAILED = [2003, "delete failed"];

    /** 账户名重复 */
    const DUPLICATE_NAME = [2004, "duplicate name"];

    /** 数据库异常 */
    const DATABASE_EXCEPTION = [4001, "datebase exception"];

    /** 认证异常 */
    const UNAUTHORIZED_EXCEPTION = [4002, "unauthorized exception"];

    /** 验证异常 */
    const VIOLATION_EXCEPTION = [4003, "violation exception"];

    /** 路由异常 */
    const ROUTER_EXCEPTION = [4004, "router exception"];
}
