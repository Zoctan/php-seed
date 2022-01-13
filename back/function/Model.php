<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once dirname(__FILE__) . "/MysqliDb.php";

class Model
{

    private $host = "localhost";
    private $username = "root";
    private $password = "root";
    private $db = "digitalduhu";
    private $port = "3306";
    private $tableName = "";
    private $mysqlidb = null;

    private function __construct()
    {
        $this->mysqlidb = new MysqliDb($this->host, $this->username, $this->password, $this->db, $this->port);
    }

    /*
     * 添加
     */
    public function create($data)
    {
        $id = $this->mysqlidb->insert("`$this->tableName`", $data);
        return $id;
    }

    /*
     * 更新
     */
    public function update($data)
    {
        $result = $this->mysqlidb
            ->where("`id`", $data["id"])
            ->update("`$this->tableName`", $data);
        return $result;
    }

    /*
     * 删除
     */
    public function delete($id)
    {
        $result = $this->mysqlidb
            ->where("`id`", $id)
            ->delete("`$this->tableName`");
        return $result;
    }
}
