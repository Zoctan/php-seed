<?php

namespace App\Model;

use App\Util\Tree;
use App\Core\AuthMember;

/**
 * AuthMemberModel
 */
class AuthMemberModel
{
    /**
     * Get authentication member by member id
     * 
     * @param $memberId
     */
    public function getByMemberId($memberId)
    {
        $memberModel = new MemberModel();
        $member = $memberModel->getById($memberModel->getColumns(), $memberId);

        $memberDataModel = new MemberDataModel();
        $memberData = $memberDataModel->getByMember_id($memberDataModel->getColumns(), $memberId);

        $memberRoleModel = new MemberRoleModel();
        $roleList = $memberRoleModel->listRoleByMemberId($memberId);

        $hasAllRule = false;
        for ($i = 0; $i < count($roleList); $i++) {
            if ($roleList[$i]['has_all_rule'] === 1) {
                $hasAllRule = true;
                break;
            }
        }

        $ruleList = [];
        if ($hasAllRule) {
            $ruleModel = new RuleModel();
            $ruleModel->select(
                $ruleModel->getColumns(),
                function ($rule) use (&$ruleList) {
                    $ruleList[] = $rule;
                }
            );
        } else {
            $ruleList = $memberRoleModel->listRuleByMemberId($memberId);
        }
        $ruleTree = Tree::list2Tree($ruleList);
        $permissionList = $memberRoleModel->getPermissionList($ruleTree);

        return new AuthMember($member, $memberData, $roleList, $permissionList);
    }
}
