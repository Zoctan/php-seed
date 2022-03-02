<?php

namespace App\Model;

use App\Core\BaseModel;

class MemberDataModel extends BaseModel
{
    protected $table = "member_data";
    protected $primary = "member_id";

    public function saveDefault($memberId)
    {
        return $this->insert(["member_id" => $memberId]);
    }
}
