<?php

namespace App\Model;

use App\Core\BaseModel;

class RoleModel extends BaseModel
{
    protected $table = 'role';
    
    public function getRuleById($roleId)
    {
        return $this->select(
            [
                '[>]role_rule' => ['role.id' => 'role_id'],
                '[>]rule' => ['role_rule.rule_id' => 'id'],
            ],
            [
                'rule.id [Int]',
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
}
