<?php

namespace App\Model;

use App\Core\BaseModel;

class RuleModel extends BaseModel
{
    protected $table = 'rule';

    public function _listBy(
        $columns = [
            'id [Int]',
            'parent_id [Int]',
            'description',
            'permission',
            'created_at',
            'updated_at'
        ],
        array $where = [],
        callable $callback = null
    ) {
        $ruleList = [];
        if ($callback === null) {
            $callback = function ($rule) use (&$ruleList) {
                $ruleList[] = $rule;
            };
        }
        $this->listBy($columns, $where, $callback);
        return $ruleList;
    }
}
