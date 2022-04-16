<?php

namespace App\Model;

use App\Util\Util;
use App\Core\BaseModel;

/**
 * RoleRuleModel
 */
class RoleRuleModel extends BaseModel
{
    protected $table = 'role_rule';

    protected $columns = [
        'id' => 'id [Int]',
        'role_id' => 'role_id [Int]',
        'rule_id' => 'rule_id [Int]',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at'
    ];

    /**
     * Update rule by role id
     * 
     * @param array $ruleIdList
     * @param mixed $roleId
     */
    public function updateRuleByRoleId($ruleIdList, $roleId)
    {
        // list old rule first
        $oldRuleList = $this->selectByRole_id($this->getColumns(), $roleId);
        $oldRuleIdList = Util::getValueList('rule_id', $oldRuleList);

        // in old list, but not in new list, delete:
        // oldRuleIdList: [1, 2, 3, 4, 5]
        //    ruleIdList: [1, 3, 5, 6, 7]
        //      diffList: [2, 4]
        if (count($oldRuleIdList) > 0) {
            $diffList = array_diff($oldRuleIdList, $ruleIdList);
            foreach ($diffList as $diffId) {
                $this->delete([
                    'AND' => [
                        'role_id' => $roleId,
                        'rule_id' => $diffId
                    ]
                ]);
            }
        }

        // in new list, but not in old list, insert:
        //    ruleIdList: [1, 3, 5, 6, 7]
        // oldRuleIdList: [1, 2, 3, 4, 5]
        //      diffList: [6, 7]
        if (count($ruleIdList) > 0) {
            $diffList2 = array_diff($ruleIdList, $oldRuleIdList);
            foreach ($diffList2 as $diffId) {
                $this->insert([
                    'role_id' => $roleId,
                    'rule_id' => $diffId
                ]);
            }
        }
    }
}
