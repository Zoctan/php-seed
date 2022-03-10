<?php

namespace App\Model;

use App\Core\BaseModel;

class TestModel extends BaseModel
{
    protected $table = "log";
}

(new TestModel())->select();
