<?php

namespace App\Model;

use App\Core\BaseModel;

class MemberOauthModel extends BaseModel
{
    protected $table = 'member_oauth';

    protected $columns = [
        'id' => 'id [Int]',
        'member_id' => 'member_id [Int]',
        'oauth_type' => 'oauth_type [Int]',
        'oauth_id' => 'oauth_id',
        'credential' => 'credential',
        'extra' => 'extra [JSON]',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at'
    ];
}
