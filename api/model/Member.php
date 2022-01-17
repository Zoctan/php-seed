<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once "../function/Singleton.php";
require_once "../function/Util.php";
require_once "../vendor/autoload.php";

class Member extends BaseModel
{
    use Singleton;

    protected $table = "member";

    private function __construct()
    {
        parent::__construct($this->table);
    }

    /*
     * 是否已登录
     */
    public static function isLogin()
    {
        $keyArray = ["id", "username", "role"];
        for ($i = 0, $len = count($keyArray); $i < $len; $i++) {
            if (!Util::inSessionOrCookie($keyArray[$i])) {
                return false;
            }
        }
        return true;
    }

    /*
     * 保存登录状态
     */
    public static function saveLoginStatus($member)
    {
        $redisCache = System::getInstance()->getValue("redisCache");
        $client = new Predis\Client($redisCache);

        // redis保存jwt，用于刷新和登出
        $client->set("foo", "bar");
        $value = $client->get("foo");
        var_dump($value);
        // cookie保存jwt
        Util::saveInSessionAndCookie($key, $value);

        // $array = [
        //     "id" => $member["id"],
        //     "username" => $member["username"],
        //     "role" => !empty($member["role"]) ? $member["role"] : "user"
        // ];
        // foreach ($array as $key => $value) {
        //     Util::saveInSessionAndCookie($key, $value);
        // }
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
                $sql .= " UPPER(`$keys[$i]`) LIKE BINARY CONCAT("%",UPPER(?),"%")";
            }
            array_push($params, $values[$i]);
        }
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
     * 创建用户
     */
    public function create($data)
    {
        $id = MysqliDb::getInstance()
            ->insert("`$this->tableName`", $data);
        return $id;
    }

    /*
     * 修改用户
     */
    public function updateById($data)
    {
        $result = MysqliDb::getInstance()
            ->where("`id`", $data["id"])
            ->update("`$this->tableName`", $data);
        return $result;
    }
}
