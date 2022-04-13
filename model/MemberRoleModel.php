<?php

namespace App\Model;

use App\Core\BaseModel;

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

    public function listRole($memberId)
    {
        return $this->select(
            [
                '[>]role' => ['member_role.role_id' => 'id'],
            ],
            [
                'role.id [Int]',
                'role.parent_id [Int]',
                'role.name',
                'role.has_all_rule [Int]',
                'role.lock [Int]',
            ],
            [
                'member_role.member_id' => $memberId
            ]
        );
    }

    public function getRule($memberId)
    {
        return $this->select(
            [
                '[>]role_rule' => ['member_role.role_id' => 'role_id'],
                '[>]rule' => ['role_rule.rule_id' => 'id'],
            ],
            [
                'rule.id [Int]',
                'rule.parent_id [Int]',
                'rule.description',
                'rule.permission',
                'rule.created_at',
                'rule.updated_at'
            ],
            [
                'member_role.member_id' => $memberId
            ]
        );
    }

    public function getPermissionList($ruleTree)
    {
        $permissionList = [];
        foreach ($ruleTree as $rule) {
            if (isset($rule['children'])) {
                foreach ($rule['children'] as $child) {
                    // [resource]:[handle], like: member:add, member:delete
                    array_push($permissionList, sprintf('%s:%s', $rule['permission'], $child['permission']));
                }
            }
        }
        return $permissionList;
    }
}
