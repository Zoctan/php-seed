<?php

namespace App\Core;

/**
 * 认证用户
 */
class AuthMember
{
    // 用户
    public $member;

    // 角色列表
    public $role;

    // 规则列表
    public $operate;

    public function __construct($member, $role, $operate)
    {
        $this->member = $member;
        $this->role = $role;
        $this->operate = $operate;
    }
}
