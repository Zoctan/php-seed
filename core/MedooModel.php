<?php

namespace App\Core;

use PDO;
use Medoo\Medoo;
use App\Core\exception\DatabaseException;

/**
 * Customize medoo
 * 
 * Call method:
 * same as medoo official document, but note:
 * $table is inherited and defined by subClass, so don't input $table when call below method.
 * 
 * Magic method:
 * 'By':
 *    using 'By' column key to call method, like: selectByColumn(), deleteByColumn() ...
 * 
 * Read for more: https://medoo.in/doc
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
abstract class MedooModel
{
    /**
     * Database name
     *
     * @var string
     */
    protected $database = 'phpseed';

    /**
     * Table name
     *
     * @var string
     */
    protected $table;

    /**
     * Table primary key
     *
     * @var string
     */
    protected $primary = 'id';

    /**
     * Table primary key type
     *
     * @var string
     */
    protected $primaryType = 'int';

    /**
     * Auto maintain created_at and updated_at or other columns
     *
     * @var string|array|bool
     */
    protected $timestamps = true;

    /**
     * Medoo config
     *
     * @var array
     */
    protected $config = [];

    /**
     * A place of connection
     *
     * @var string
     */
    protected $place = 'MedooModel';

    /**
     * Read method
     *
     * @var bool
     */
    protected $read = false;

    /**
     * Write method
     *
     * @var bool
     */
    protected $write = false;

    public function __construct(array $config)
    {
        if (empty($config)) {
            throw new DatabaseException('Empty database config');
        }
        $this->config = $config;
        $this->database = $config['master'][0]['database'];

        return $this;
    }

    /**
     * Insert
     * 
     * @param array $values
     * @return int|string|void
     */
    public function insert(array $values)
    {
        $this->write = true;
        $this->connection()->insert($this->table, $values);
        $id = $this->id();
        if (empty($id)) {
            throw new DatabaseException('Insert error');
        }
        return $id;
    }

    /**
     * Get page
     *
     * @param int $currentPage
     * @param int $pageSize
     * @param string|array $columns
     * @param array $where
     * @return array
     */
    public function page(int $currentPage = 0, int $pageSize = 20, $columns = '*', array $where = []): array
    {
        if ($pageSize > 0) {
            $limitStart = ($currentPage > 0 ? $currentPage - 1 : 0) * $pageSize;
            // list id first
            $where['LIMIT'] = [$limitStart, $pageSize];
            $ids = $this->connection()->select($this->table, $this->primary, $where);
            $total = count($ids);
            $list = $this->connection()->select($this->table, $columns, [$this->primary => $ids]);
            return [
                'list' => $list,
                'total' => $total,
                'currentPage' => $currentPage,
                'pageSize' => $pageSize,
                'totalPage' => ceil($total / $pageSize),
            ];
        } else {
            $list = $this->connection()->select($this->table, $columns, $where);
            $total = count($list);
            return [
                'list' => $list,
                'total' => $total,
            ];
        }
    }

    /**
     * Get page (join other table)
     *
     * @param int $currentPage
     * @param int $pageSize
     * @param array $join
     * @param string|array $columns
     * @param array $where
     *
     * @return array
     */
    public function pageJoin(int $currentPage = 0, int $pageSize = 20, array $join = [], $columns = '*', array $where = []): array
    {
        if ($pageSize > 0) {
            $limitStart = ($currentPage > 0 ? $currentPage - 1 : 0) * $pageSize;
            $where['LIMIT'] = [$limitStart, $pageSize];
            $idList = [];
            $this->connection()->select(
                $this->table,
                $join,
                "$this->table.$this->primary",
                $where,
                function ($_id) use (&$idList) {
                    $idList[] = $_id;
                }
            );
            $total = count($idList);
            $list = [];
            $this->connection()->select(
                $this->table,
                $join,
                $columns,
                ["$this->table.$this->primary" => $idList],
                function ($_data) use (&$list) {
                    $list[] = $_data;
                }
            );
            return [
                'list' => $list,
                'total' => $total,
                'currentPage' => $currentPage,
                'pageSize' => $pageSize,
                'totalPage' => ceil($total / $pageSize),
            ];
        } else {
            $list = $this->connection()->select($this->table, $join, $columns, $where);
            $total = count($list);
            return [
                'list' => $list,
                'total' => $total,
            ];
        }
    }

    /**
     * Error
     * 
     * @return string
     */
    public function error()
    {
        return $this->connection()->error;
    }

    /**
     * Error detail
     * 
     * @return array|null
     */
    public function errorInfo()
    {
        return $this->connection()->errorInfo;
    }

    /**
     * For more information about PDO class, read more about from: http://php.net/manual/en/class.pdo.php
     * 
     * @return PDO
     */
    public function pdo()
    {
        return $this->connection()->pdo;
    }

    /**
     * Return all executed queries
     *
     * @return array
     */
    public function log()
    {
        return $this->connection()->log();
    }

    /**
     * Return the last query performed
     *
     * @return mixed
     */
    public function last()
    {
        return $this->connection()->last();
    }

    /**
     * Return the ID for the last inserted row
     *
     * @return int|string|void
     */
    public function id()
    {
        $id = $this->connection()->id();
        switch ($this->primaryType) {
            case 'int':
                return intval($id);
            case 'str':
                return strval($id);
        }
        return null;
    }

    /**
     * Execute the customized raw query
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
     * Quotes the string for the query
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
     * Database connection information
     *
     * @return array
     */
    public function info()
    {
        return $this->connection()->info();
    }

    /**
     * Start a transaction
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
     * Enable debug mode and output readable statement string
     *
     * @return $this
     */
    public function debug()
    {
        $this->connection()->debug();
        return $this;
    }

    /**
     * Get table name
     *
     * @return mixed
     */
    public function getTable()
    {
        return $this->config[$this->database][$this->read ? 'master' : 'slave'][0]['prefix'] . $this->table;
    }

    /**
     * Set table name
     *
     * @param $table
     */
    public function setTable($table)
    {
        $this->table = ltrim($table, $this->config[$this->database][$this->read ? 'master' : 'slave'][0]['prefix']);
    }

    /**
     * call method using By key
     *
     * @param string &$method
     * @param array &$arguments
     */
    private function callMethodUsingByKey(&$method, &$arguments)
    {
        // use 'By' to set $where directly
        // like: 
        // selectByName(array|string $columns, $name)
        // selectByNameGender(array|string $columns, [$name, $gender])
        if (strpos($method, 'By') !== false) {
            list($method, $whereKeyString) = explode('By', $method);
            if ($whereKeyString === '') {
                throw new DatabaseException('Empty where string when using "By".');
            } else {
                $whereKeyList = preg_split("/(?=[A-Z])/", $whereKeyString, -1, PREG_SPLIT_NO_EMPTY);
            }
            for ($i = 0; $i < count($whereKeyList); $i++) {
                $whereKeyList[$i] = strtolower($whereKeyList[$i]);
            }
            $wherePosition = null;
            switch ($method) {
                case 'select':
                    // select(array|string $columns, callable $callback)
                    if (count($arguments) === 2) {
                        if (!is_callable($arguments[1])) {
                            $wherePosition = 1;
                        }
                    }
                    // select(array|string $columns, array $where, callable $callback)
                    else if (count($arguments) === 3) {
                        $wherePosition = 1;
                    }
                    // select(array $join, array $columns, array $where, callable $callback)
                    else if (count($arguments) === 4) {
                        $wherePosition = 2;
                    }
                    break;
                case 'delete':
                    // delete(array $where)
                    $wherePosition = 0;
                    break;
                case 'update':
                case 'replace':
                    // update(array $values, array $where)
                    // replace(array|string $columns, array $where)
                    $wherePosition = 1;
                    break;
                case 'has':
                case 'count':
                    // has(array $where)
                    // count(array $where)
                    if (count($arguments) === 1) {
                        $wherePosition = 0;
                    }
                    // has(array $join, array $where)
                    // count(array $join, array $where)
                    else if (count($arguments) === 2) {
                        $wherePosition = 1;
                    }
                    break;
                case 'get':
                case 'rand':
                case 'max':
                case 'min':
                case 'avg':
                case 'sum':
                    // get(array|string $columns, array $where)
                    // rand(array|string $column, array $where)
                    // max(string $column, array $where)
                    // min(string $column, array $where)
                    // avg(string $column, array $where)
                    // sum(string $column, array $where)
                    if (count($arguments) === 2) {
                        $wherePosition = 1;
                    }
                    // get(array $join, array|string $columns, array $where)
                    // rand(array $join, array|string $column, array $where)
                    // max(array $join, string $column, array $where)
                    // min(array $join, string $column, array $where)
                    // avg(array $join, string $column, array $where)
                    // sum(array $join, string $column, array $where)
                    if (count($arguments) === 3) {
                        $wherePosition = 2;
                    }
                    break;
            }
            if (is_numeric($wherePosition)) {
                $whereValueList = $arguments[$wherePosition];
                if (!is_array($whereValueList)) {
                    $whereValueList = [$whereValueList];
                }
                // 'name' => 'test'
                if (count($whereKeyList) === count($whereValueList)) {
                    $where = array_combine($whereKeyList, $whereValueList);
                }
                // 'name' => ['test', 'demo', 'xxx']
                else if (count($whereKeyList) === 1 && count($whereValueList) > 1) {
                    $where = [$whereKeyList[0] => $whereValueList];
                } else {
                    throw new DatabaseException('Where key list not equal value list when using "By".');
                }
                $arguments[$wherePosition] = $where;
            }
        }
    }

    /**
     * Magic function
     * 
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $this->callMethodUsingByKey($method, $arguments);

        // read method
        $this->read = in_array($method, ['select', 'get', 'has', 'count',  'sum', 'max', 'min', 'avg']);

        // write method
        $this->write = in_array($method, ['insert', 'update', 'replace']);

        // table name first
        $arguments = array_merge([$this->table], $arguments);

        $this->maintainTimestamps($method, $arguments[1]);

        return call_user_func_array([$this->connection(), $method], $arguments);
    }

    /**
     * Maintain timestamp key
     *
     * @param $method
     * @param $data
     * @return array
     */
    protected function maintainTimestamps($method, &$data)
    {
        $timestamp = Medoo::raw('NOW()');
        $times = [];

        if ($this->write && $this->timestamps) {
            if (is_bool($this->timestamps)) {
                $times = ['updated_at' => $timestamp];
                $method == 'insert' && $times['created_at'] = $timestamp;
            } elseif (is_array($this->timestamps)) {
                foreach ($this->timestamps as $item) {
                    $times[$item] = $timestamp;
                }
            } elseif (is_string($this->timestamps)) {
                $times[$this->timestamps] = $timestamp;
            }
        }


        $multi = $method == 'insert' && is_array($data) && is_numeric(array_keys($data)[0]);
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
     * Connection
     * 
     * @return Medoo
     */
    protected function connection()
    {
        if (!isset($_ENV[$this->place]) || !isset($_ENV[$this->place][$this->database])) {
            $master = $this->config['master'];
            $master = $master[array_rand($master)];
            $master['database'] = $this->database;

            $_ENV[$this->place][$this->database]['master'] = new Medoo($master);

            $slave = $this->config['slave'];
            $slave = $slave[array_rand($slave)];
            if (!empty($slave)) {
                $slave['database'] = $this->database;
            }

            if (empty($slave) || empty(array_diff($master, $slave))) {
                $_ENV[$this->place][$this->database]['slave'] = &$_ENV[$this->place][$this->database]['master'];
            } else {
                $_ENV[$this->place][$this->database]['slave'] = new Medoo($slave);
            }
        }

        return $_ENV[$this->place][$this->database][$this->read ? 'slave' : 'master'];
    }

    /**
     * Clear
     *
     * @return void
     */
    public function __destruct()
    {
        $_ENV[$this->place] = null;
    }
}
