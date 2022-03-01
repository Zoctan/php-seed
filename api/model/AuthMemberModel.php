<?php

namespace App\Model;

use App\Core\AuthMember;

class AuthMemberModel
{
    public function get($memberId)
    {
        $memberDataModel = new MemberDataModel();
        $memberData = $memberDataModel->getBy(["member_id [Int]", "avatar", "nickname", "gender [Int]"], ["member_id" => $memberId]);

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

        return new AuthMember($memberData, $role, $permissionList);
    }
}
