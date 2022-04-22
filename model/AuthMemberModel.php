<?php

namespace App\Model;

use App\Util\Util;
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
     * @param mixed $memberId
     */
    public function getByMemberId($memberId)
    {
        $memberModel = new MemberModel();
        $member = $memberModel->getById($memberModel->getColumnsExcept('password'), $memberId);

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
        $ruleModel = new RuleModel();
        $ruleList = [];
        if ($hasAllRule) {
            $ruleModel->select(
                $ruleModel->getColumns(),
                function ($rule) use (&$ruleList) {
                    $ruleList[] = $rule;
                }
            );
        } else {
            $ruleList = $memberRoleModel->listRuleByMemberId($memberId);
            $ruleParentIdList = Util::getValueAsListByKey('parent_id', $ruleList);
            $parentRuleList = $ruleModel->listParentByParentIdList($ruleParentIdList, $ruleModel->getColumns());
            $ruleList = array_merge($ruleList, $parentRuleList);
        }
        $ruleTree = Tree::list2Tree($ruleList);
        $permissionList = $memberRoleModel->getPermissionList($ruleTree);

        return new AuthMember($member, $memberData, $roleList, $permissionList);
    }
}
