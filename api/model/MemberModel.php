<?php

namespace App\Model;

use Medoo\Medoo;

use App\Core\BaseModel;

class MemberModel extends BaseModel
{
    protected $table = "member";

    public function getByUsername($username)
    {
        return $this->mysql->get($this->table, ["id [Int]", "username", "password", "status [Int]"], ["username" => $username]);
    }

    public function updateLoginTimeById($id)
    {
        return $this->updateBy(["login_at" => Medoo::raw("NOW()")], ["id" => $id]);
    }

    public function getRole($memberId)
    {
        return $this->mysql->get(
            "member",
            [
                "[>]member_role" => ["id" => "member_id"],
                "[>]role" => ["member_role.role_id" => "id"],
            ],
            [
                "role.id [Int]",
                "role.name"
            ],
            [
                "member.id" => $memberId
            ]
        );
    }

    public function getRule($memberId)
    {
        return $this->mysql->select(
            "member",
            [
                "[>]member_role" => ["id" => "member_id"],
                "[>]role_rule" => ["member_role.role_id" => "role_id"],
                "[>]rule" => ["role_rule.rule_id" => "id"],
            ],
            [
                "rule.id [Int]",
                "rule.description",
                "rule.operate"
            ],
            [
                "member.id" => $memberId
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

    public function verifyPassword($password, $passwordDB)
    {
        return password_verify($password, $passwordDB);
    }
}
