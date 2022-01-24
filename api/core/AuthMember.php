<?php

namespace App\Core;

/**
 * 认证用户
 */
class AuthMember
{
    /**
     * 用户
     * 
     * @var 
     */
    public $member;

    /**
     * 角色
     * 
     * @var string
     */
    public $role;

    /**
     * 规则列表
     * 
     * @var array
     */
    public $operate;

    public function __construct($member, $role, $operate)
    {
        $this->member = $member;
        $this->role = $role;
        $this->operate = $operate;
    }
}
