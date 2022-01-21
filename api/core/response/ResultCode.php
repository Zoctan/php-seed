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
    const SUCCEED = [0, "成功请求"];

    /** 失败请求 */
    const FAILED = [1, "失败请求"];

    /** 未知异常 */
    const UNKNOWN_FAILED = [2, "未知异常"];

    /** 成功请求，但结果不是期望的成功结果 */
    const SUCCEED_REQUEST_FAILED_RESULT = [1000, "成功请求，但结果不是期望的成功结果"];

    /** 查询失败 */
    const FIND_FAILED = [2000, "查询失败"];

    /** 保存失败 */
    const SAVE_FAILED = [2001, "保存失败"];

    /** 更新失败 */
    const UPDATE_FAILED = [2002, "更新失败"];

    /** 删除失败 */
    const DELETE_FAILED = [2003, "删除失败"];

    /** 账户名重复 */
    const DUPLICATE_NAME = [2004, "账户名重复"];

    /** 数据库异常 */
    const DATABASE_EXCEPTION = [4001, "数据库异常"];

    /** 认证异常 */
    const UNAUTHORIZED_EXCEPTION = [4002, "认证异常"];

    /** 验证异常 */
    const VIOLATION_EXCEPTION = [4003, "验证异常"];

    /** 路由异常 */
    const ROUTER_EXCEPTION = [4004, "路由异常"];
}
