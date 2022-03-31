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
        $method = is_array($key) ? "select" : "getBy";
        return $this->$method(["value [JSON]"], ["key" => $key]);
    }

    /*
     * 添加
     */
    public function add($description, $key, $value)
    {
        return $this->insert([
            "description" => $description,
            "key" => $key,
            "value [JSON]" => $value
        ]);
    }

    /*
     * 更新值
     */
    public function updateValue($key, $value)
    {
        $this->updateBy(["value [JSON]" => $value], ["key" => $key]);
    }

    /*
     * 更新
     */
    public function updateById($values, $id): void
    {
        $this->updateById([
            "key" => $values["key"],
            "value [JSON]" => $values["value"],
            "description" => $values["description"],
        ], $id);
    }
}
