<?php

namespace App\Model;

use App\Core\BaseModel;

/**
 * MemberRoleModel
 */
class MemberRoleModel extends BaseModel
{
    protected $table = 'member_role';

    protected $columns = [
        'id' => 'id [Int]',
        'member_id' => 'member_id [Int]',
        'role_id' => 'role_id [Int]',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at'
    ];

    /**
     * List role by member id
     * 
     * @param mixed $memberId
     * @return array
     */
    public function listRoleByMemberId($memberId)
    {
        $roleModel = new RoleModel();
        return $this->select(
            [
                '[>]role' => ['member_role.role_id' => 'id'],
            ],
            $roleModel->getColumns(),
            [
                'member_role.member_id' => $memberId
            ]
        );
    }

    /**
     * List rule by member id
     * 
     * @param mixed $memberId
     * @return array
     */
    public function listRuleByMemberId($memberId)
    {
        $ruleModel = new RuleModel();
        return $this->select(
            [
                '[>]role_rule' => ['member_role.role_id' => 'role_id'],
                '[>]rule' => ['role_rule.rule_id' => 'id'],
            ],
            $ruleModel->getColumns(),
            [
                'member_role.member_id' => $memberId
            ]
        );
    }

    /**
     * Get permission list
     * 
     * @param array $ruleTree
     * @return array
     */
    public function getPermissionList($ruleTree)
    {
        $permissionList = [];
        foreach ($ruleTree as $rule) {
            if (isset($rule['children'])) {
                foreach ($rule['children'] as $child) {
                    // [resource]:[handle], like: member:add, member:remove
                    array_push($permissionList, sprintf('%s:%s', $rule['permission'], $child['permission']));
                }
            }
        }
        return $permissionList;
    }
}
