<?php

namespace App\Model;

use App\Core\BaseModel;

class SystemModel extends BaseModel
{
    protected $table = 'system';

    protected $columns = [
        'id' => 'id [Int]',
        'description' => 'description',
        'key' => 'key',
        'value' => 'value [JSON]',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at'
    ];

    /**
     * 获取值
     * 
     * @param string|array $key
     *
     * @return string|array
     */
    public function getValue($key)
    {
        $method = is_array($key) ? 'selectByKey' : 'getByKey';
        return $this->$method($this->getColumns(['value']), $key);
    }

    public function add($description, $key, $value)
    {
        return $this->insert([
            'description' => $description,
            'key' => $key,
            'value [JSON]' => $value
        ]);
    }

    public function updateValue($key, $value)
    {
        $this->updateByKey(['value [JSON]' => $value], $key);
    }

    public function updateById($values, $id): void
    {
        parent::updateById([
            'key' => $values['key'],
            'value [JSON]' => $values['value'],
            'description' => $values['description'],
        ], $id);
    }
}
