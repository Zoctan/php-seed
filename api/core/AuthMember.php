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
     * @var
     */
    public $role;

    /**
     * 操作权限列表
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

    /**
     * 是否有操作权限
     * 
     * @param string $authOperate
     */
    public function has($authOperate)
    {
        return in_array($authOperate, $this->operate);
    }
}
