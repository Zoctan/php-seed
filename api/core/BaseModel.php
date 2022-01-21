<?php

namespace App\Core;

/**
 * select($table, $columns)
 * select($table, $columns, $callback)
 * select($table, $columns, $where)
 * select($table, $columns, $where, $callback)
 * select($table, $join, $columns, $where)
 * select($table, $join, $columns, $where, $callback)
 * https://medoo.in/api/select
 * 
 * insert($table, $values)
 * https://medoo.in/api/insert
 * 
 * delete($table, $where)
 * https://medoo.in/api/delete
 * 
 * update($table, $values, $where)
 * https://medoo.in/api/update
 * 
 * get($table, $columns, $where)
 * get($table, $join, $columns, $where)
 * https://medoo.in/api/get
 */

use Medoo\Medoo;

/**
 * 异常模型类
 */
class BaseModel
{
    protected $table = "";

    /**
     * @var Medoo
     */
    protected $mysql;

    public function __construct()
    {
        $this->mysql = \App\DI()->mysql;
    }

    public function save(...$values)
    {
        $this->mysql->insert($this->table, $values);
        if (count($values) == 1) {
            return $this->mysql->id();
        }
    }

    public function deleteBy($where): int
    {
        $result =  $this->mysql->delete($this->table, $where);
        return $result->rowCount();
    }

    public function deleteById($id): int
    {
        return $this->deleteBy(["id" => $id]);
    }

    public function updateBy($values, $where): int
    {
        $result =  $this->mysql->update($this->table, $values, $where);
        return $result->rowCount();
    }

    public function updateById($values, $id): int
    {
        return $this->updateBy($values, ["id" => $id]);
    }

    public function getById($columns = "*", $id)
    {
        return $this->mysql->get($this->table, $columns, ["id" => $id]);
    }

    public function listAll($columns = "*", $where = [])
    {
        $this->mysql->select($this->table, $columns, $where);
    }
}
