<?php

namespace App\Core;

/**
 * 认证成员
 */
class AuthMember
{
    /**
     * 成员信息
     * 
     * @var object
     */
    public $memberData;

    /**
     * 角色
     * 
     * @var object
     */
    public $role;

    /**
     * 操作权限列表
     * 
     * @var array
     */
    public $permissionList;

    public function __construct($memberData, $role, $permissionList)
    {
        $this->memberData = $memberData;
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
