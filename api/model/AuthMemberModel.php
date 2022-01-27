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
            var_dump($rules);
        } else {
            $rules = $memberRoleModel->getRule($memberId);
        }
        $operate = $memberRoleModel->getOperate($rules);
        return new AuthMember($member, $role, $operate);
    }
}
