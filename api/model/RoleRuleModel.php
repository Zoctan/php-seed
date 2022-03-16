<?php

namespace App\Model;

use App\Util\Util;
use App\Core\BaseModel;

class RoleRuleModel extends BaseModel
{
    protected $table = "role_rule";

    public function updateRuleByRoleId($ruleIdList, $roleId)
    {
        // 先找到原来的规则列表
        $oldRuleList = $this->listByRoleId($roleId, ["rule_id [Int]"]);
        $oldRuleIdList = Util::value2Array($oldRuleList, "rule_id");
        // 在 原来规则，但不在 新规则，要删除
        // oldRuleIdList: [1, 2, 3, 4, 5]
        //    ruleIdList: [1, 3, 5, 6, 7]
        //      diffList: [2, 4]
        $diffList = array_diff($oldRuleIdList, $ruleIdList);
        foreach ($diffList as $diffId) {
            $this->deleteBy([
                "AND" => [
                    "role_id" => $roleId,
                    "rule_id" => $diffId
                ]
            ]);
        }
        // 在 新规则，但不在 原来规则，要添加
        //    ruleIdList: [1, 3, 5, 6, 7]
        // oldRuleIdList: [1, 2, 3, 4, 5]
        //      diffList: [6, 7]
        $diffList2 = array_diff($ruleIdList, $oldRuleIdList);
        foreach ($diffList2 as $diffId) {
            $this->insert([
                "role_id" => $roleId,
                "rule_id" => $diffId
            ]);
        }
    }

    public function listByRoleId(
        $roleId,
        $column = [
            "id [Int]",
            "role_id [Int]",
            "rule_id [Int]",
            "created_at"
        ]
    ) {
        return $this->select(
            $column,
            [
                "role_id" => $roleId
            ]
        );
    }
}
