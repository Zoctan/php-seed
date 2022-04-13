<?php

namespace App\Model;

use App\Core\BaseModel;

class RuleModel extends BaseModel
{
    protected $table = 'rule';

    protected $columns = [
        'id' => 'id [Int]',
        'parent_id' => 'parent_id [Int]',
        'description' => 'description',
        'permission' => 'permission',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at'
    ];
}
