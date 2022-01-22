<?php

namespace App\Model;

use App\Core\BaseModel;

class MemberRoleModel extends BaseModel
{
    protected $table = "member_role";

    public function saveAsDefaultRole($memberId)
    {
        return $this->save(["member_id" => $memberId]);
    }

    public function getRole($memberId)
    {
        return $this->mysql->get(
            "member_role",
            [
                "[>]role" => ["role_id" => "id"],
            ],
            [
                "role.id [Int]",
                "role.name"
            ],
            [
                "member_role.id" => $memberId
            ]
        );
    }

    public function getRule($memberId)
    {
        return $this->mysql->select(
            "member_role",
            [
                "[>]role_rule" => ["role_id" => "role_id"],
                "[>]rule" => ["role_rule.rule_id" => "id"],
            ],
            [
                "rule.id [Int]",
                "rule.description",
                "rule.operate"
            ],
            [
                "member_role.id" => $memberId
            ]
        );
    }

    public function getOperate($rules)
    {
        $operate = [];
        foreach ($rules as $rule) {
            array_push($operate, $rule["operate"]);
        }
        return $operate;
    }
}
