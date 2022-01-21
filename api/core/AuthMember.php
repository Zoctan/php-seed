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
    protected $roles;

    // 规则列表
    protected $rules;

    public function __construct($member, $roles, $rules)
    {
        $this->member = $member;
        $this->roles = $roles;
        $this->rules = $rules;
    }
}
