<?php

namespace App\Model;

use App\Core\BaseModel;

class MemberDataModel extends BaseModel
{
    protected $table = 'member_data';

    protected $columns = [
        'id' => 'id [Int]',
        'member_id' => 'member_id [Int]',
        'avatar' => 'avatar',
        'nickname' => 'nickname',
        'gender' => 'gender [Int]',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at'
    ];
}
