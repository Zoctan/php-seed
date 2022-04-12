<?php

namespace App\Model;

use Medoo\Medoo;

use App\Core\BaseModel;

class MemberModel extends BaseModel
{
    protected string $table = 'member';

    protected array $columns = [
        'id' => 'id [Int]',
        'username' => 'username',
        'password' => 'password',
        'status'   => 'status [Int]',
        'logined_at' => 'logined_at',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at'
    ];

    public function add($member)
    {
        $member['password'] = password_hash($member['password'], PASSWORD_DEFAULT);
        $memberId = $this->insert($member);

        // 绑定默认角色
        $memberRoleModel = new MemberRoleModel();
        $memberRoleModel->saveAsDefaultRole($memberId);
        // 绑定默认数据
        $memberDataModel = new MemberDataModel();
        $memberDataModel->saveDefault($memberId);
        return $memberId;
    }

    public function updateByMemberId($member)
    {
        if ($member['password'] != null) {
            $member['password'] = password_hash($member['password'], PASSWORD_DEFAULT);
        }
        $this->updateById($member, $member['id']);
    }

    /**
     * 根据账户名获取成员
     */
    public function getByUsername(
        $username,
       array $columnKeys
    ) {
        return $this->getBy(
            $this->getColumns($columnKeys),
            [
                'username' => $username
            ]
        );
    }

    /**
     * 更新登录时间
     */
    public function updateLoginedAtById($id)
    {
        $this->updateBy(['logined_at' => Medoo::raw('NOW()')], ['id' => $id]);
    }

    /**
     * 校验密码
     */
    public function verifyPassword($password, $passwordDB)
    {
        return password_verify($password, $passwordDB);
    }

    public function deleteByMemberId($memberId)
    {
        $this->deleteById($memberId);
        $memberDataModel = new MemberDataModel();
        $memberDataModel->deleteBy(['member_id' => $memberId]);

        $memberRoleModel = new MemberRoleModel();
        $memberRoleModel->deleteBy(['member_id' => $memberId]);
    }
}
