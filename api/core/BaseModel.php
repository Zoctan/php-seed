<?php

namespace PHPSeed\Core;

use PDO;
use Medoo\Medoo;

/*
 * select($table, $columns)
 * select($table, $columns, $callback)
 * select($table, $columns, $where)
 * select($table, $columns, $where, $callback)
 * select($table, $join, $columns, $where)
 * select($table, $join, $columns, $where, $callback)
 * https://medoo.in/api/select
 * 
 * 
 * get($table, $columns, $where)
 * get($table, $join, $columns, $where)
 * https://medoo.in/api/get
 * 
 * 
 * insert($table, $values)
 * https://medoo.in/api/insert
 * 
 * 
 * update($table, $values, $where)
 * https://medoo.in/api/update
 * 
 * 
 * delete($table, $where)
 * https://medoo.in/api/delete
 */

class BaseModel
{
    protected $database = null;
    protected $table = "";

    protected function __construct($table)
    {
        // https://medoo.in/api/new
        $this->database = new Medoo([
            "type" => "mysql",
            "host" => "localhost",
            "database" => "digitalduhu",
            "username" => "root",
            "password" => "root",

            // [optional]
            "charset" => "utf8mb4",
            "collation" => "utf8mb4_unicode_ci",
            "port" => 3306,

            // [optional] Table prefix, all table names will be prefixed as PREFIX_table.
            //"prefix" => "PREFIX_",

            // [optional] Enable logging, it is disabled by default for better performance.
            "logging" => true,

            // [optional]
            // Error mode
            // Error handling strategies when error is occurred.
            // PDO::ERRMODE_SILENT (default) | PDO::ERRMODE_WARNING | PDO::ERRMODE_EXCEPTION
            // Read more from https://www.php.net/manual/en/pdo.error-handling.php.
            "error" => PDO::ERRMODE_SILENT,

            // [optional]
            // The driver_option for connection.
            // Read more from http://www.php.net/manual/en/pdo.setattribute.php.
            "option" => [
                // PDO::ATTR_CASE：强制列名为指定的大小写
                //      PDO::CASE_NATURAL：保留数据库驱动返回的列名
                PDO::ATTR_CASE => PDO::CASE_NATURAL
            ],

            // [optional] Medoo will execute those commands after connected to the database.
            "command" => [
                "SET SQL_MODE=ANSI_QUOTES"
            ]
        ]);
        $this->table = $table;
    }
}
