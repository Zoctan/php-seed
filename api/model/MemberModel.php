<?php

namespace App\Model;

use App\Core\BaseModel;

class MemberModel extends BaseModel
{
    protected $table = "member";

    public function getByUsername($username)
    {
        return $this->mysql->get($this->table, "*", $username);
    }

    public function updateLoginTimeById($id)
    {
        return $this->updateBy(["login_at" => "NOW()"], ["id" => $id]);
    }

    public function getRole()
    {
        
        return;
    }

    public function getRule()
    {
        return;
    }

    public function verifyPassword($password, $passwordDB)
    {
        return password_verify($password, $passwordDB);
    }
}
