<?php

namespace App\Model;

use Medoo\Medoo;
use App\Core\BaseModel;

/**
 * MemberModel
 */
class MemberModel extends BaseModel
{
    protected $table = 'member';

    protected $columns = [
        'id' => 'id [Int]',
        'username' => 'username',
        'password' => 'password',
        'status' => 'status [Int]',
        'lock' => 'lock [Int]',
        'logined_at' => 'logined_at',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at'
    ];

    /**
     * Add member
     * 
     * @param array $member
     * @return mixed id
     */
    public function add($member)
    {
        $member['password'] = password_hash($member['password'], PASSWORD_DEFAULT);
        $memberId = $this->insert($member);

        // bind default member role
        $memberRoleModel = new MemberRoleModel();
        $memberRoleModel->insert(['member_id' => $memberId]);

        // bind default member data
        $memberDataModel = new MemberDataModel();
        $memberDataModel->insert(['member_id' => $memberId]);
        return $memberId;
    }

    /**
     * Update member by id
     * 
     * @param array $member
     */
    public function updateById($member)
    {
        if ($member['password'] != null) {
            $member['password'] = password_hash($member['password'], PASSWORD_DEFAULT);
        }
        parent::updateById($member, $member['id']);
    }

    /**
     * Update member logined time by id
     * 
     * @param mixed $id
     */
    public function updateLoginedAtById($id)
    {
        parent::updateById(['logined_at' => Medoo::raw('NOW()')], $id);
    }

    /**
     * Verify member password
     * 
     * @param string $password
     * @param string $passwordDB
     * @return bool
     */
    public function verifyPassword($password, $passwordDB)
    {
        return password_verify($password, $passwordDB);
    }
}
