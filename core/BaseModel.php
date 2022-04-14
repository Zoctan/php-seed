<?php

namespace App\Core;

/**
 * Base model
 */
class BaseModel extends MedooModel
{

    protected $columns = [];

    public function __construct()
    {
        parent::__construct(\App\DI()->config['datasource']['mysql']);
    }

    public function getColumns($columnKeys = [])
    {
        $columnKeys = $this->splitIfString($columnKeys);
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

    public function getColumnsExcept($columnExceptKeys = [])
    {
        $columnExceptKeys = $this->splitIfString($columnExceptKeys);
        $columnKeys = array_values(array_diff(array_keys($this->columns), $columnExceptKeys));
        return $this->getColumns($columnKeys);
    }

    private function splitIfString($keys)
    {
        if (is_string($keys)) {
            if (strpos('&', $keys) !== false) {
                return explode('&', $keys);
            } else if (strpos(',', $keys) !== false) {
                return explode(',', $keys);
            } else {
                return [$keys];
            }
        }
        return $keys;
    }
}
