<?php

namespace App\Core;

/**
 * 模型基类
 */
class BaseModel extends MedooModel
{

    public function __construct()
    {
        parent::__construct(\App\DI()->config['datasource']['mysql']);
    }
}