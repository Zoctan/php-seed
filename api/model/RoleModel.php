<?php

namespace App\Model;

use App\Core\BaseModel;

class RoleModel extends BaseModel
{
    protected $table = "role";

    public function add($role)
    {
        $roleId = $this->insert($role);
        if (!$roleId) {
            throw new \Exception("insert role error");
        }
        return $roleId;
    }

    public function getRule($roleId)
    {
        return $this->select(
            [
                "[>]role_rule" => ["role_id" => "id"],
                "[>]rule" => ["role_rule.rule_id" => "id"],
            ],
            [
                "rule.id [Int]",
                "rule.description",
                "rule.permission",
                "rule.created_at",
                "rule.updated_at"
            ],
            [
                "role.id" => $roleId
            ]
        );
    }
}
