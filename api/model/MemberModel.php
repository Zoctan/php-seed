<?php

namespace App\Model;

use Medoo\Medoo;

use App\Core\BaseModel;

class MemberModel extends BaseModel
{
    protected $table = "member";

    /**
     * 添加
     */
    public function add($member)
    {
        $member["password"] = password_hash($member["password"], PASSWORD_DEFAULT);
        $memberId = $this->insert($member);
        if (!$memberId) {
            throw new \Exception("成员创建失败");
        }
        // 绑定默认角色
        $memberRoleModel = new MemberRoleModel();
        $memberRoleModel->saveAsDefaultRole($memberId);
        // 绑定默认数据
        $memberDataModel = new MemberDataModel();
        $memberDataModel->saveDefault($memberId);
        return $memberId;
    }

    /**
     * 根据账户名获取成员
     */
    public function getByUsername(
        $username,
        $column = [
            "id [Int]",
            "username",
            "password",
            "status [Int]",
            "logined_at",
            "created_at",
            "updated_at"
        ]
    ) {
        return $this->getBy(
            $column,
            [
                "username" => $username
            ]
        );
    }

    /**
     * 更新登录时间
     */
    public function updateLoginedAtById($id)
    {
        $this->updateBy(["logined_at" => Medoo::raw("NOW()")], ["id" => $id]);
    }

    /**
     * 校验密码
     */
    public function verifyPassword($password, $passwordDB)
    {
        return password_verify($password, $passwordDB);
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
}
