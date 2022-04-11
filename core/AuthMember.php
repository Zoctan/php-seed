<?php

namespace App\Core;

/**
 * 认证成员
 */
class AuthMember
{
    /**
     * 成员
     * 
     * @var object
     */
    public $member;

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

    public function __construct($member, $memberData, $role, $permissionList)
    {
        $this->member = $member;
        $this->memberData = $memberData;
        $this->role = $role;
        $this->permissionList = $permissionList;
    }

    /**
     * 是否有操作权限
     * 
     * @param array $need
     */
    public function checkPermission(array $need)
    {
        if (count($need) == 0) {
            return true;
        }
        $intersect = array_intersect($need, $this->permissionList);
        \App\debug('intersect', $intersect);
        if (isset($need['joint'])) {
            if ($need['joint'] === 'and') {
                return count($intersect) === count($need);
            } else if ($need['joint'] === 'or') {
                return count($intersect) > 0;
            }
        } else {
            // same as joint = 'and'
            return count($intersect) === count($need);
        }
        return false;
    }
}
