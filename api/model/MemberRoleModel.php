<?php

namespace App\Model;

use App\Core\BaseModel;

class MemberRoleModel extends BaseModel
{
    protected $table = "member_role";

    public function saveAsDefaultRole($memberId)
    {
        return $this->insert(["member_id" => $memberId]);
    }

    public function getRole($memberId)
    {
        return $this->get(
            [
                "[>]role" => ["role_id" => "id"],
            ],
            [
                "role.id [Int]",
                "role.name",
                "role.has_all_rule [Int]",
                "role.lock [Int]",
            ],
            [
                "member_role.member_id" => $memberId
            ]
        );
    }

    public function getRule($memberId)
    {
        return $this->select(
            [
                "[>]role_rule" => ["role_id" => "role_id"],
                "[>]rule" => ["role_rule.rule_id" => "id"],
            ],
            [
                "rule.id [Int]",
                "rule.description",
                "rule.permission"
            ],
            [
                "member_role.member_id" => $memberId
            ]
        );
    }

    public function getPermissionList($rules)
    {
        $permissionList = [];
        foreach ($rules as $rule) {
            array_push($permissionList, $rule["permission"]);
        }
        return $permissionList;
    }
}
