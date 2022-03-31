<?php

namespace App\Model;

use App\Core\BaseModel;

class RuleModel extends BaseModel
{
    protected $table = "rule";
    
    public function listAllWithoutCondition()
    {
        $ruleList = [];
        $this->listBy([
            "id [Int]",
            "description",
            "permission"
        ], [], function ($rule) use (&$ruleList) {
            $ruleList[] = $rule;
        });
        return $ruleList;
    }
}
