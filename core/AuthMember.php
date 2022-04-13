<?php

namespace App\Core;

/**
 * 认证成员
 */
class AuthMember
{
    /**
     * member
     * 
     * @var object
     */
    public $member;

    /**
     * member data
     * 
     * @var object
     */
    public $memberData;

    /**
     * role list
     * 
     * @var object
     */
    public $roleList;

    /**
     * permission list
     * 
     * @var array
     */
    public $permissionList;

    public function __construct($member, $memberData, $roleList, $permissionList)
    {
        $this->member = $member;
        $this->memberData = $memberData;
        $this->roleList = $roleList;
        $this->permissionList = $permissionList;
    }

    /**
     * check member has target permission
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
