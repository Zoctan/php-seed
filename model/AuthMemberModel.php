<?php

namespace App\Model;

use App\Util\Tree;
use App\Core\AuthMember;

class AuthMemberModel
{
    public function get($memberId)
    {
        $memberModel = new MemberModel();
        $member = $memberModel->getBy(['id [Int]', 'username', 'status [Int]', 'member.lock [Int]', 'logined_at', 'created_at', 'updated_at'], ['id' => $memberId]);

        $memberDataModel = new MemberDataModel();
        $memberData = $memberDataModel->getBy(['avatar', 'nickname', 'gender [Int]'], ['member_id' => $memberId]);

        $memberRoleModel = new MemberRoleModel();
        $role = $memberRoleModel->getRole($memberId);

        $ruleList = [];
        if ($role['has_all_rule'] == 1) {
            $ruleModel = new RuleModel();
            $ruleList = $ruleModel->_listBy();
        } else {
            $ruleList = $memberRoleModel->getRule($memberId);
        }
        $ruleTree = Tree::list2Tree($ruleList);
        $permissionList = $memberRoleModel->getPermissionList($ruleTree);

        return new AuthMember($member, $memberData, $role, $permissionList);
    }
}
