<?php

namespace App\Controller;

use App\Model\RoleModel;
use App\Model\RuleModel;
use App\Model\RoleRuleModel;
use App\Model\MemberRoleModel;
use App\Core\BaseController;
use App\Core\Response\ResultGenerator;

class RoleController extends BaseController
{
    /**
     * @var RoleModel
     */
    private $roleModel;
    /**
     * @var RuleModel
     */
    private $ruleModel;
    /**
     * @var RoleRuleModel
     */
    private $roleRuleModel;

    public function __construct(RoleModel $roleModel, RuleModel $ruleModel, RoleRuleModel $roleRuleModel)
    {
        parent::__construct();
        $this->roleModel = $roleModel;
        $this->ruleModel = $ruleModel;
        $this->roleRuleModel = $roleRuleModel;
    }

    public function addRole()
    {
        $name = strval($this->request->get("name"));
        $hasAllRule = intval($this->request->get("hasAllRule", 0));
        $lock = intval($this->request->get("lock", 0));

        if (empty($name)) {
            return ResultGenerator::errorWithMsg("please input role name");
        }

        $roleId = $this->roleModel->add([
            "name" => $name,
            "has_all_rule" => $hasAllRule,
            "lock" => $lock,
        ]);

        return ResultGenerator::successWithData($roleId);
    }

    public function addRule()
    {
        $description = strval($this->request->get("description"));
        $permission = strval($this->request->get("permission"));

        if (empty($description) || empty($permission)) {
            return ResultGenerator::errorWithMsg("please input description and permission");
        }

        $ruleId = $this->ruleModel->add([
            "description" => $description,
            "permission" => $permission,
        ]);

        return ResultGenerator::successWithData($ruleId);
    }

    public function listRole()
    {
        $currentPage = intval($this->request->get("currentPage", 0));
        $pageSize = intval($this->request->get("pageSize", 20));

        $name = strval($this->request->get("name"));
        $hasAllRule = intval($this->request->get("hasAllRule", 0));
        $lock = intval($this->request->get("lock", 0));

        $where = [];
        if ($name) {
            $where["name[~]"] = $name;
        }
        if ($hasAllRule) {
            $where["has_all_rule"] = $hasAllRule;
        }
        if ($lock) {
            $where["lock"] = $lock;
        }

        $result = $this->roleModel->page(
            $currentPage,
            $pageSize,
            [
                "id [Int]",
                "name",
                "has_all_rule [Int]",
                "lock [Int]",
                "created_at",
                "updated_at",
            ],
            $where
        );
        return ResultGenerator::successWithData($result);
    }

    public function listRule()
    {
        $ruleList = $this->ruleModel->listAllWithoutCondition();
        return ResultGenerator::successWithData($ruleList);
    }

    public function detail()
    {
        $roleId = intval($this->request->get("roleId"));
        if (empty($roleId)) {
            return ResultGenerator::errorWithMsg("role id doesn't exist");
        }
        $role = $this->roleModel->getById([
            "id [Int]",
            "name",
            "has_all_rule [Int]",
            "lock [Int]",
            "created_at",
            "updated_at",
        ], $roleId);
        if (empty($role)) {
            return ResultGenerator::errorWithMsg("role doesn't exist");
        }
        $ruleList = $this->roleModel->getRuleById($roleId);
        if (!empty($ruleList) && $ruleList[0]["id"] === null) {
            $ruleList = [];
        }
        return ResultGenerator::successWithData([
            "role" => $role,
            "ruleList" => $ruleList,
        ]);
    }

    public function updateRole()
    {
        $role = $this->request->get("role");
        if (empty($role)) {
            return ResultGenerator::errorWithMsg("role doesn't exist");
        }
        $ruleList = $this->request->get("ruleList");

        $this->roleModel->updateById($role, $role["id"]);
        $this->roleRuleModel->updateRuleByRoleId($ruleList, $role["id"]);
        return ResultGenerator::success();
    }

    public function updateRule()
    {
        $roleId = intval($this->request->get("roleId"));
        if (empty($roleId)) {
            return ResultGenerator::errorWithMsg("role id doesn't exist");
        }
        $ruleIdList = $this->request->get("ruleIdList");
        $this->roleRuleModel->updateRuleById($ruleIdList, $roleId);
        return ResultGenerator::success();
    }

    public function updateMemberRole()
    {
        $memberId = intval($this->request->get("memberId"));
        $role = $this->request->get("role");
        if (empty($memberId) || empty($role)) {
            return ResultGenerator::errorWithMsg("member id or role id doesn't exist");
        }
        $memberRoleModel = new  MemberRoleModel();
        $memberRoleModel->updateBy(
            [
                "role_id" => $role["id"],
            ],
            [
                "member_id" => $memberId,
            ]
        );
        return ResultGenerator::success();
    }

    public function delete()
    {
        $roleId = intval($this->request->get("roleId"));
        if (empty($roleId)) {
            return ResultGenerator::errorWithMsg("role id doesn't exist");
        }
        $this->roleModel->deleteById($roleId);
        return ResultGenerator::success();
    }
}
