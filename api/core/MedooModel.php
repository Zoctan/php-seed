<?php

namespace App\Core;

use PDO;
use Medoo\Medoo;

/**
 * https://medoo.in/doc
 * 
 * // Supported data type: [String | Bool | Int | Number | Object | JSON]
 * // [String] is the default type for all output data.
 * // [Object] is a PHP object data decoded by serialize(), and will be unserialize()
 * // [JSON] is a valid JSON, and will be json_decode()
 * 
 * @method array select(string $table, array|string $columns, array $where)
 * @method null select(string $table, array|string $columns, callable $callback)
 * @method null select(string $table, array|string $columns, array $where, callable $callback)
 * @method null select(string $table, array $join, array $columns, array $where, callable $callback)
 * 
 * @method mixed get(string $table, array|string $columns, array $where)
 * @method mixed get(string $table, array $join, array|string $columns, array $where)
 * 
 * @method mixed insert(string $table, array $values)
 * 
 * @method mixed delete(string $table, array $where)
 * 
 * @method mixed update(string $table, array $values, array $where)
 * 
 * @method mixed replace(string $table, array|string $columns, array $where)
 * 
 * @method bool has(string $table, array $where)
 * @method bool has(string $table, array $join, array $where)
 * 
 * @method mixed rand(string $table, array|string $column, array $where)
 * @method mixed rand(string $table, array $join, array|string $column, array $where)
 * 
 * @method int count(string $table, array $where)
 * @method int count(string $table, array $join, array $where)
 * 
 * @method int max(string $table, string $column)
 * @method int max(string $table, array $join, string $column)
 * 
 * @method int min(string $table, string $column)
 * @method int min(string $table, array $join, string $column)
 * 
 * @method int avg(string $table, string $column)
 * @method int avg(string $table, array $join, string $column)
 * 
 * @method int sum(string $table, string $column)
 * @method int sum(string $table, array $join, string $column)
 * 
 * @method int max(string $table, string $column, array $where)
 * @method int max(string $table, array $join, string $column, array $where)
 * 
 * @method int min(string $table, string $column, array $where)
 * @method int min(string $table, array $join, string $column, array $where)
 * 
 * @method int avg(string $table, string $column, array $where)
 * @method int avg(string $table, array $join, string $column, array $where)
 * 
 * @method int sum(string $table, string $column, array $where)
 * @method int sum(string $table, array $join, string $column, array $where)
 */

/**
 * 模型基类
 */
abstract class MedooModel
{
    /**
     * 库名
     *
     * @var string
     */
    protected $database = "phpseed";

    /**
     * 表名
     *
     * @var string
     */
    protected $table;

    /**
     * 表主键
     *
     * @var string
     */
    protected $primary = "id";

    /**
     * 表主键类型
     *
     * @var string
     */
    protected $primaryType = "int";

    /**
     * 自动维护 created_at 和 updated_at，或其他指定字段
     *
     * @var string|array|bool
     */
    protected $timestamps = true;

    /**
     * 配置
     *
     * @var array
     */
    protected $config = [];

    /**
     * a place of connect
     *
     * @var string
     */
    protected $place = "MedooModel";

    /**
     * 是否是读操作
     *
     * @var bool
     */
    protected $read = false;

    /**
     * 是否是写操作
     *
     * @var bool
     */
    protected $write = false;

    public function __construct(array $config)
    {
        if (!$config) {
            throw new \Exception("配置不能为空");
        }
        $this->config = $config;

        return $this;
    }

    /**
     * 错误信息
     * 
     * @return string
     */
    public function error()
    {
        return $this->connection()->error;
    }

    /**
     * 详细错误信息
     * 
     * @return array|null
     */
    public function errorInfo()
    {
        return $this->connection()->errorInfo;
    }

    /**
     * 获取数据库连接实例
     *
     * @return PDO
     */
    public function pdo()
    {
        return $this->connection()->pdo;
    }

    /**
     * 插入数据
     *
     * @param array $values
     *
     * @return int|void
     */
    public function insert(array $values)
    {
        $this->write = true;
        $this->connection()->insert($this->table, $values);
        return $this->id();
    }

    /**
     * 根据条件查询一条数据
     *
     * @param string $columns
     * @param array $where
     *
     * @return mixed
     */
    public function getBy($columns = "*", array $where): mixed
    {
        if (empty($where)) throw new \Exception("条件为空，查询单条数据失败");

        $this->read = true;
        return $this->connection()->get($this->table, $columns, $where);
    }

    /**
     * 根据主键查询一条数据
     *
     * @param string $columns
     * @param $id
     *
     * @return mixed
     */
    public function getById($columns = "*", $id): mixed
    {
        return $this->getBy($columns, [$this->primary => $id]);
    }

    /**
     * 根据条件删除一条数据
     *
     * @param array $where
     *
     * @return void
     */
    public function deleteBy(array $where): void
    {
        if (empty($where)) throw new \Exception("条件为空，删除失败");

        $result = $this->connection()->delete($this->table, $where);
        if ($result->rowCount() != 1) {
            throw new \Exception("删除失败");
        }
    }

    /**
     * 根据主键删除一条数据
     *
     * @param $id
     *
     * @return void
     */
    public function deleteById($id): void
    {
        $this->deleteBy([$this->primary => $id]);
    }

    /**
     * 根据条件更新数据
     *
     * @param array $where
     *
     * @return void
     */
    public function updateBy($values, array $where): void
    {
        if (empty($where)) throw new \Exception("条件为空，更新失败");

        $this->write = true;
        $result = $this->connection()->update($this->table, $values, $where);
        if ($result->rowCount() != 1) {
            throw new \Exception("更新失败");
        }
    }

    /**
     * 根据主键更新数据
     *
     * @param $id
     *
     * @return void
     */
    public function updateById($values, $id): void
    {
        $this->updateBy($values, [$this->primary => $id]);
    }

    /**
     * 获取 sql 执行记录
     *
     * @return array
     */
    public function log()
    {
        return $this->connection()->log();
    }

    /**
     * 获取最后一条 sql
     *
     * @return mixed
     */
    public function last()
    {
        return $this->connection()->last();
    }

    /**
     * 获取最新插入的 id
     *
     * @return int|string
     */
    public function id()
    {
        $id = $this->connection()->id();
        switch ($this->primaryType) {
            case 'int':
                return intval($id);
            default:
            case 'str':
                return strval($id);
        }
    }

    /**
     * 自定义查询
     *
     * @param $query
     *
     * @return bool|\PDOStatement
     */
    public function query($query)
    {
        return $this->connection()->query($query);
    }

    /**
     * 转义字符串, 供 query 使用
     *
     * @param $string
     *
     * @return string
     */
    public function quote($string)
    {
        return $this->connection()->quote($string);
    }

    /**
     * 获取数据库信息
     *
     * @return array
     */
    public function info()
    {
        return $this->connection()->info();
    }

    /**
     * 事务
     *
     * @param $callback
     *
     * @return bool
     */
    public function action($callback)
    {
        return $this->connection()->action($callback);
    }

    /**
     * 开启 debug 模式
     *
     * @return $this
     */
    public function debug()
    {
        $this->connection()->debug();

        return $this;
    }

    /**
     * 获取表名
     *
     * @return mixed
     */
    public function getTable()
    {
        return $this->config[$this->database][$this->read ? "master" : "slave"][0]["prefix"] . $this->table;
    }

    /**
     * 设置表名
     *
     * @param $table
     */
    public function setTable($table)
    {
        $this->table = ltrim($table, $this->config[$this->database][$this->read ? "master" : "slave"][0]["prefix"]);
    }

    /**
     * Medoo 调用代理
     *
     * @param $method
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        // 是否是读操作
        $this->read = in_array($method, ["select", "get", "has", "count",  "sum", "max", "min", "avg"]);

        // 是否是写操作
        $this->write = in_array($method, ["insert", "update", "replace"]);

        // 第一个是表名
        $arguments = array_merge([$this->table], $arguments);

        // 自动维护数据库 插入更新时间
        $this->appendTimestamps($method, $arguments[1]);

        return call_user_func_array([$this->connection(), $method], $arguments);
    }

    /**
     * 自动维护数据库 插入更新时间
     *
     * @param $method
     * @param $data
     *
     * @return array
     */
    protected function appendTimestamps($method, &$data)
    {
        $timestamp = Medoo::raw('NOW()');
        $times = [];

        if ($this->write && $this->timestamps) {
            if (is_bool($this->timestamps)) {
                $times = ["updated_at" => $timestamp];
                $method == "insert" && $times["created_at"] = $timestamp;
            } elseif (is_array($this->timestamps)) {
                foreach ($this->timestamps as $item) {
                    $times[$item] = $timestamp;
                }
            } elseif (is_string($this->timestamps)) {
                $times[$this->timestamps] = $timestamp;
            }
        }

        $multi = $method == "insert" && is_array($data) && is_numeric(array_keys($data)[0]);
        if ($times) {
            if ($multi) {
                foreach ($data as &$item) {
                    $item = array_merge($item, $times);
                }
            } else {
                $data = array_merge($data, $times);
            }
        }

        return $data;
    }

    /**
     * 连接实例
     * 
     * @return Medoo
     */
    protected function connection()
    {
        if (!isset($_ENV[$this->place]) || !isset($_ENV[$this->place][$this->database])) {
            $master = $this->config[$this->database]["master"];
            $master = $master[array_rand($master)];

            $slave = $this->config[$this->database]["slave"];
            $slave = $slave[array_rand($slave)];

            $_ENV[$this->place][$this->database]["master"] = new Medoo($master);

            if (empty(array_diff($master, $slave))) {
                $_ENV[$this->place][$this->database]["slave"] = &$_ENV[$this->place][$this->database]["master"];
            } else {
                $_ENV[$this->place][$this->database]["slave"] = new Medoo($slave);
            }
        }

        return $_ENV[$this->place][$this->database][$this->read ? "slave" : "master"];
    }

    /**
     * 清理资源
     *
     * @return void
     */
    public function __destruct()
    {
        $_ENV[$this->place] = null;
    }
}
