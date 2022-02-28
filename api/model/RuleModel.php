<?php

namespace App\Model;

use App\Core\BaseModel;

class RuleModel extends BaseModel
{
    protected $table = "rule";

    public function listAllWithoutCondition()
    {
        $rules = [];
        $this->listAll([
            "id [Int]",
            "description",
            "permission"
        ], [], function ($rule) use (&$rules)  {
            $rules[] = $rule;
        });
        return $rules;
    }
}
