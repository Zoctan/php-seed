<?php

namespace App\Model;

use App\Util\Util;
use App\Core\BaseModel;

/**
 * RuleModel
 */
class RuleModel extends BaseModel
{
    protected $table = 'rule';

    protected $columns = [
        'id' => 'id [Int]',
        'parent_id' => 'parent_id [Int]',
        'description' => 'description',
        'permission' => 'permission',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at'
    ];

    /**
     * List parent by parent id list
     * 
     * 
     * @param array $parentIdList
     * @param array $columns
     * @return array
     */
    public function listParentByParentIdList($parentIdList, $columns)
    {
        $parentList = [];
        while (true) {
            $result = $this->select($columns, ['rule.id' => $parentIdList]);
            $parentList = array_merge($parentList, $result);
            $parentIdList2 = Util::getValueAsListByKey('parent_id', $result);
            $noZeroParentIdList = array_filter($parentIdList2, function ($parentId) {
                return $parentId !== 0;
            });
            if (empty($noZeroParentIdList)) {
                break;
            }
        }
        return $parentList;
    }
}
