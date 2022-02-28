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
    public $permissionList;

    public function __construct($member, $role, $permissionList)
    {
        $this->member = $member;
        $this->role = $role;
        $this->permissionList = $permissionList;
    }

    /**
     * 是否有操作权限
     * 
     * @param array $need
     */
    public function has(array $need)
    {
        if (count($need) == 0) {
            return true;
        }
        return count(array_diff($need, $this->permissionList)) == 0;
    }
}
