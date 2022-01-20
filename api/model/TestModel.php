<?php

namespace PHPSeed\Model;

use PHPSeed\Core\BaseModel;

class TestModel extends BaseModel
{
    protected $table = "log";

    public function select()
    {
        var_dump($this->listAll());
    }
}

(new TestModel())->select();
