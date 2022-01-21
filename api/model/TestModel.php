<?php

namespace App\Model;

use App\Core\BaseModel;

class TestModel extends BaseModel
{
    protected $table = "log";

    public function select()
    {
        var_dump($this->listAll());
    }
}

(new TestModel())->select();
