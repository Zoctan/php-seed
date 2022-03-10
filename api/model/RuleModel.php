<?php

namespace App\Model;

use App\Core\BaseModel;

class RuleModel extends BaseModel
{
    protected $table = "rule";

    public function add($rule)
    {
        $ruleId = $this->insert($rule);
        if (!$ruleId) {
            throw new \Exception("insert rule error");
        }
        return $ruleId;
    }

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
