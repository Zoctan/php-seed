<?php

namespace App\Model;

use App\Core\BaseModel;

class SystemModel extends BaseModel
{
    protected $table = "system";

    /**
     * 获取值
     * 
     * @param string|array $key
     *
     * @return string|array
     */
    public function getValue($key)
    {
        $method = is_array($key) ? "getBy" : "select";
        return $this->$method(["value [JSON]"], ["key", $key]);
    }

    /*
     * 添加
     */
    public function add($description, $key, $value)
    {
        $id = $this->insert([
            "description" => $description,
            "key" => $key,
            "value [JSON]" => $value
        ]);
        if (!$id) {
            throw new \Exception("系统键值对创建失败");
        }
        return $id;
    }

    /*
     * 更新值
     */
    public function updateValue($key, $value)
    {
        return $this->updateBy(["value [JSON]" =>  $value], ["key" => $key]);
    }

    /*
     * 更新
     */
    public function updateById($values, $id): void
    {
        $this->updateById([
            "description" => $values["description"],
            "key" => $values["key"],
            "value [JSON]" => $values["value"],
        ], $id);
    }
}
