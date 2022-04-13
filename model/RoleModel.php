<?php

namespace App\Model;

use App\Core\BaseModel;

class RoleModel extends BaseModel
{
    protected $table = 'role';

    protected $columns = [
        'id' => 'id [Int]',
        'parent_id' => 'parent_id [Int]',
        'name' => 'name',
        'has_all_rule' => 'has_all_rule [Int]',
        'lock' => 'lock [Int]',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at'
    ];

    public function listRuleByRoleId($roleId)
    {
        return $this->select(
            [
                '[>]role_rule' => ['role.id' => 'role_id'],
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
                'role.id' => $roleId
            ]
        );
    }

    public function listParentByParentId($parentId, $columns)
    {
        $parentList = [];
        while (true) {
            $parent = $this->getById($columns, $parentId);
            $parentList[] = $parent;
            $parentId = $parent['parent_id'];
            if ($parentId === 0) {
                break;
            }
        }
        return $parentList;
    }
}
