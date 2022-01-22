<?php

namespace App\Core;

/**
 * 认证用户
 */
class AuthMember
{
    // 用户
    protected $member;

    // 角色列表
    protected $role;

    // 规则列表
    protected $rule;

    public function __construct($member, $roles, $rule)
    {
        $this->member = $member;
        $this->role = $roles;
        $this->rule = $rule;
    }
}
