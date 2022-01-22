<?php

namespace App\Core;

/**
 * // Supported data type: [String | Bool | Int | Number | Object | JSON]
 * // [String] is the default type for all output data.
 * // [Object] is a PHP object data decoded by serialize(), and will be unserialize()
 * // [JSON] is a valid JSON, and will be json_decode()
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

    public function deleteBy($where): void
    {
        $result = $this->mysql->delete($this->table, $where);
        if ($result->rowCount() != 1) {
            throw new \Exception("删除失败");
        }
    }

    public function deleteById($id): void
    {
        $this->deleteBy(["id" => $id]);
    }

    public function updateBy($values, $where): void
    {
        $result = $this->mysql->update($this->table, $values, $where);
        if ($result->rowCount() != 1) {
            throw new \Exception("更新失败");
        }
    }

    public function updateById($values, $id): void
    {
        $this->updateBy($values, ["id" => $id]);
    }

    public function getBy($columns = "*", $where)
    {
        return $this->mysql->get($this->table, $columns, $where);
    }

    public function getById($columns = "*", $id)
    {
        return $this->getBy($columns, ["id" => $id]);
    }

    public function listBy($columns = "*", $where = [])
    {
        $this->mysql->select($this->table, $columns, $where);
    }
}
