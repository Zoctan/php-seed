<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once dirname(__FILE__) . "/../function/MysqliDb.php";

class System
{
    // 单例
    private static $instance;
    private $tableName = "system";

    // 获取实例
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    /**
     * 获取
     */
    public function getOne($key)
    {
        return MysqliDb::getInstance()
            ->where("`key`", $key)
            ->getOne("`$this->tableName`");
    }

    /**
     * 获取全部
     */
    public function getAll()
    {
        return MysqliDb::getInstance()->get("`$this->tableName`");
    }

    /**
     * 获取值
     */
    public function getValue($key)
    {
        $system = MysqliDb::getInstance()
            ->where("`key`", $key)
            ->getOne("`$this->tableName`");
        return json_decode($system["value"], true);
    }

    /**
     * 获取值列表
     */
    public function getValues($keys)
    {
        $keyList = explode(",", $keys);
        MysqliDb::getInstance()->where("`key`", $keyList[0]);
        for ($i = 1, $len = count($keyList); $i < $len; $i++) {
            MysqliDb::getInstance()->orWhere("`key`", $keyList[$i]);
        }
        $systemList =  MysqliDb::getInstance()->get("`$this->tableName`");

        $resultList = [];
        for ($i = 0, $len = count($systemList); $i < $len; $i++) {
            $resultList = array_merge($resultList, [
                $systemList[$i]["key"] => json_decode($systemList[$i]["value"], true)
            ]);
        }
        return $resultList;
    }

    /*
     * 添加
     */
    public function create($description, $key, $value)
    {
        $id = MysqliDb::getInstance()
            ->insert("`$this->tableName`", [
                "description" => $description,
                "key" => $key,
                "value" => json_encode($value),
            ]);
        return $id;
    }

    /*
     * 更新值
     */
    public function updateValue($key, $value)
    {
        $result = MysqliDb::getInstance()
            ->where("`key`", $key)
            ->update("`$this->tableName`", [
                "value" => json_encode($value),
            ]);
        return $result;
    }

    /*
     * 更新
     */
    public function update($id, $description, $key, $value)
    {
        $result = MysqliDb::getInstance()
            ->where("`id`", $id)
            ->update("`$this->tableName`", [
                "description" => $description,
                "key" => $key,
                "value" => json_encode($value),
            ]);
        return $result;
    }

    /*
     * 删除
     */
    public function delete($id)
    {
        $result = MysqliDb::getInstance()
            ->where("`id`", $id)
            ->delete("`$this->tableName`");
        return $result;
    }
}
