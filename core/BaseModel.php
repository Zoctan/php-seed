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

    public function getColumns(array $columnKeys = [])
    {
        $columnValues = [];
        $keysLength = count($columnValues);
        if ($keysLength > 0) {
            for ($i = 0; $i < $keysLength; $i++) {
                array_push($columnValues, $this->column[$columnKeys[$i]]);
            }
        } else {
            $columnValues = array_values($this->column);
        }
        return $columnValues;
    }
}
