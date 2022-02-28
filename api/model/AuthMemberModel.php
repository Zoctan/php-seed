<?php

namespace App\Model;

use App\Core\AuthMember;

class AuthMemberModel
{
    public function get($memberId)
    {
        $memberModel = new MemberModel();
        $member = $memberModel->getById(["id [Int]", "username", "status [Int]"], $memberId);

        $memberRoleModel = new MemberRoleModel();
        $role = $memberRoleModel->getRole($memberId);
        
        $rules = [];
        if ($role["has_all_rule"] == 1) {
            $ruleModel = new RuleModel();
            $rules = $ruleModel->listAllWithoutCondition();
        } else {
            $rules = $memberRoleModel->getRule($memberId);
        }
        $permissionList = $memberRoleModel->getPermissionList($rules);
        
        return new AuthMember($member, $role, $permissionList);
    }
}
