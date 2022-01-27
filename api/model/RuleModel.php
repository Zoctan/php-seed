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
            "operate"
        ], [], function ($rule) use (&$rules)  {
            $rules[] = $rule;
            var_dump($rule);
        });
        return $rules;
    }
}
