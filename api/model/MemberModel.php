<?php

namespace App\Model;

use Medoo\Medoo;

use App\Core\BaseModel;

class MemberModel extends BaseModel
{
    protected $table = "member";

    public function save($member)
    {
        $member["password"] = password_hash($member["password"], PASSWORD_DEFAULT);
        $memberId = $this->save($member);
        if (!$memberId) {
            throw new \Exception("成员创建失败");
        }
        // 绑定默认角色
        $memberRoleModel = new MemberRoleModel();
        $memberRoleModel->saveAsDefaultRole($memberId);
        return $memberId;
    }

    public function getByUsername($username)
    {
        return $this->mysql->get($this->table, ["id [Int]", "username", "password", "status [Int]"], ["username" => $username]);
    }

    public function updateLoginTimeById($id)
    {
        return $this->updateBy(["login_at" => Medoo::raw("NOW()")], ["id" => $id]);
    }

    public function verifyPassword($password, $passwordDB)
    {
        return password_verify($password, $passwordDB);
    }
}
