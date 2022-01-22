<?php

namespace App\Model;

use App\Core\AuthMember;

class AuthMemberModel
{
    public function get($memberId)
    {
        $memberModel = new MemberModel();
        $member = $memberModel->getById(["id", "username", "status"], $memberId);
        
        $memberRoleModel = new MemberRoleModel();
        $role = $memberRoleModel->getRole($member["id"]);
        $rules = $memberRoleModel->getRule($member["id"]);
        $operate = $memberRoleModel->getOperate($rules);

        return new AuthMember($member, $role, $operate);
    }
}
