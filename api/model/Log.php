<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once dirname(__FILE__) . "/../function/Util.php";
require_once dirname(__FILE__) . "/../function/MysqliDb.php";
require_once dirname(__FILE__) . "/../function/IpDb/Ipv4Location.php";
require_once dirname(__FILE__) . "/Member.php";

class Log
{
    // 单例
    private static $instance;
    private $tableName = "log";

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
     * 分页查询
     */
    public function pageBy($currentPage = 0, $pageSize = 20, $keys = [], $values = [], $like = false)
    {
        $resultList = $this->getBy($keys, $values, $like);
        // 数据总条数
        $total = count($resultList);

        // 有分页
        if (!empty($currentPage) && $currentPage > 0) {
            $currentPage = (int) $currentPage;
            $pageSize = (int) $pageSize;
            $limitStart = ($currentPage - 1) * $pageSize;
            $resultList = $this->getBy($keys, $values, $like, $limitStart, $pageSize);
        }

        return Util::page($currentPage, $pageSize, $total, $resultList);
    }

    public function getBy($keys = [], $values = [], $like = false, $limitStart = -1, $limitSize = -1)
    {
        $len = count($values);
        $params = [];
        $sql = "SELECT * FROM `$this->tableName` ";
        $sql .= $len > 0 ? " WHERE " : "";
        for ($i = 0; $i < $len; $i++) {
            if ($i != 0) {
                $sql .= " AND ";
            }
            if (!$like) {
                $sql .= " `$keys[$i]` = ?";
            } else {
                $sql .= " UPPER(`$keys[$i]`) LIKE BINARY CONCAT('%',UPPER(?),'%')";
            }
            array_push($params, $values[$i]);
        }
        $sql .= " ORDER BY `id` DESC";
        if ($limitStart != -1 && $limitSize != -1) {
            $sql .= " LIMIT ?, ?";
            array_push($params, $limitStart, $limitSize);
        }
        return MysqliDb::getInstance()
            ->rawQuery($sql, count($params) > 0 ? $params : null);
    }

    public function getOneBy($keys = [], $values = [], $like = false)
    {
        $resultList = $this->getBy($keys, $values, $like);
        return count($resultList) == 0 ? null : $resultList[0];
    }

    /*
     * 添加
     */
    public function create($data)
    {
        $member =  Member::getInstance()->getOneBy(["id"], [$data["member_id"]]);

        $ip = Util::getIp();
        $ipInfo = Ipv4Location::getLocation($ip);

        $ipAddress = [$ipInfo["country"], $ipInfo["province"], $ipInfo["city"]];
        $ipCity = [];
        for ($i = 0; $i < count($ipAddress); $i++) {
            if (!empty($ipAddress[$i])) {
                array_push($ipCity, $ipAddress[$i]);
            }
        }

        $data =  array_merge($data, [
            "member_name" =>  $member["nickname"],
            "ip" => ip2long($ip),
            "ip_city" => implode("-", $ipCity),
        ]);

        $id = MysqliDb::getInstance()
            ->insert("`$this->tableName`", $data);

        return $id;
    }


    public function info($memberId, $content, $extra = null)
    {
        return $this->create([
            "level" => 0,
            "member_id" => (int) $memberId,
            "content" => $content,
            "extra" => $extra,
        ]);
    }


    public function warn($memberId, $content, $extra = null)
    {
        return $this->create([
            "level" => 1,
            "member_id" => (int)  $memberId,
            "content" => $content,
            "extra" => $extra,
        ]);
    }


    public function error($memberId, $content, $extra = null)
    {
        return $this->create([
            "level" => 2,
            "member_id" => (int)  $memberId,
            "content" => $content,
            "extra" => $extra,
        ]);
    }

    /*
     * 更新
     */
    public function update($data)
    {
        $result = MysqliDb::getInstance()
            ->where("`id`", $data["id"])
            ->update("`$this->tableName`", $data);
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
