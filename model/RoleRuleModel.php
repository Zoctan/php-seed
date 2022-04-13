<?php

namespace App\Model;

use App\Util\Util;
use App\Core\BaseModel;

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

    public function updateRuleByRoleId($ruleIdList, $roleId)
    {
        // 先找到原来的规则列表
        $oldRuleList = $this->selectByRole_id($this->getColumns(), $roleId);
        $oldRuleIdList = Util::getValueList('rule_id', $oldRuleList);

        // 在 原来规则，但不在 新规则，要删除
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

        // 在 新规则，但不在 原来规则，要添加
        //    ruleIdList: [1, 3, 5, 6, 7]
        // oldRuleIdList: [1, 2, 3, 4, 5]
        //      diffList: [6, 7]
        $diffList2 = array_diff($ruleIdList, $oldRuleIdList);
        foreach ($diffList2 as $diffId) {
            $this->insert([
                'role_id' => $roleId,
                'rule_id' => $diffId
            ]);
        }
    }
}
