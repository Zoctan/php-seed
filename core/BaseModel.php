<?php

namespace App\Core;

/**
 * 模型基类
 */
class BaseModel extends MedooModel
{

    protected $columns = [];

    public function __construct()
    {
        parent::__construct(\App\DI()->config['datasource']['mysql']);
    }

    public function getColumns(array $columnKeys = [])
    {
        $columnValues = [];
        $keysLength = count($columnKeys);
        if ($keysLength > 0) {
            for ($i = 0; $i < $keysLength; $i++) {
                $columnValues[] = $this->columns[$columnKeys[$i]];
            }
        } else {
            $columnValues = array_values($this->columns);
        }
        // add table name to column value, eg. member.name, member.id
        for ($i = 0; $i < count($columnValues); $i++) {
            $columnValues[$i] = sprintf('%s.%s', $this->table, $columnValues[$i]);
        }
        return $columnValues;
    }

    public function getColumnsExcept(array $columnExceptKeys = [])
    {
        $columnKeys = array_values(array_diff(array_keys($this->columns), $columnExceptKeys));
        return $this->getColumns($columnKeys);
    }
}
