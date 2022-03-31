<?php

namespace App\Model;

use App\Core\BaseModel;

class RuleModel extends BaseModel
{
    protected $table = 'rule';
    
    public function listAllWithoutCondition(
        $columns = [
            'id [Int]',
            'parent_id [Int]',
            'description',
            'permission',
            'created_at',
            'updated_at'
        ]
    ) {
        $ruleList = [];
        $this->listBy($columns, [], function ($rule) use (&$ruleList) {
            $ruleList[] = $rule;
        });
        return $ruleList;
    }
}
