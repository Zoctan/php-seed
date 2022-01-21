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

    public function updateLoginTimeByName($username)
    {
        $result =  $this->mysql->update($this->table, ["login_at" => ], ["username" => $username]);
        return $result->rowCount();
    }

    public function verifyPassword($password, $passwordDB)
    {
        return password_verify($password, $passwordDB);
    }
}
