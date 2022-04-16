<?php

namespace App\Model;

use App\Core\BaseModel;

/**
 * PairModel
 */
class PairModel extends BaseModel
{
    protected $table = 'pair';

    protected $columns = [
        'id' => 'id [Int]',
        'description' => 'description',
        'key' => 'key',
        'value' => 'value [JSON]',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at'
    ];

    /**
     * Get value
     * 
     * @param string|array $key
     * @return string|array value
     */
    public function getValue($key)
    {
        $method = is_array($key) ? 'selectByKey' : 'getByKey';
        return $this->$method($this->getColumns('value'), $key);
    }

    /**
     * Add pair
     * 
     * @param string $description
     * @param string $key
     * @param mixed $value
     * @return mixed id
     */
    public function add($description, $key, $value)
    {
        return $this->insert([
            'description' => $description,
            'key' => $key,
            'value [JSON]' => $value
        ]);
    }

    /**
     * Update value
     * 
     * @param string $key
     * @param mixed $value
     */
    public function updateValue($key, $value)
    {
        parent::updateByKey(['value [JSON]' => $value], $key);
    }

    /**
     * Update by id
     * 
     * @param array $data
     * @param mixed $id
     */
    public function updateById($data, $id)
    {
        parent::updateById([
            'description' => $data['description'],
            'key' => $data['key'],
            'value [JSON]' => $data['value'],
        ], $id);
    }
}
