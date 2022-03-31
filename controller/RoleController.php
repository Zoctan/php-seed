<?php

namespace App\Controller;

use App\Model\RoleModel;
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
     * @var RoleRuleModel
     */
    private $roleRuleModel;

    public function __construct(RoleModel $roleModel, RoleRuleModel $roleRuleModel)
    {
        parent::__construct();
        $this->roleModel = $roleModel;
        $this->roleRuleModel = $roleRuleModel;
    }

    public function add()
    {
        $role = $this->request->get('role');
        $ruleList = $this->request->get('ruleList');

        if (empty($role)) {
            return ResultGenerator::errorWithMsg('role doesn't exist');
        }

        $roleId = $this->roleModel->insert($role);

        $this->roleRuleModel->updateRuleByRoleId($ruleList, $roleId);
        return ResultGenerator::successWithData($roleId);
    }

    public function list()
    {
        $currentPage = intval($this->request->get('currentPage', 0));
        $pageSize = intval($this->request->get('pageSize', 20));

        $role = $this->request->get('role');

        $where = [];
        if ($role) {
            if (is_numeric($role['id'])) {
                $where['id'] = $role['id'];
            }
            if (!empty($role['name'])) {
                $where['name[~]'] = $role['name'];
            }
            if (is_numeric($role['has_all_rule'])) {
                $where['has_all_rule'] = $role['has_all_rule'];
            }
            if (is_numeric($role['lock'])) {
                $where['lock'] = $role['lock'];
            }
        }

        $result = $this->roleModel->page(
            $currentPage,
            $pageSize,
            [
                'id [Int]',
                'name',
                'has_all_rule [Int]',
                'lock [Int]',
                'created_at',
                'updated_at',
            ],
            $where
        );
        return ResultGenerator::successWithData($result);
    }

    public function detail()
    {
        $roleId = intval($this->request->get('roleId'));
        if (empty($roleId)) {
            return ResultGenerator::errorWithMsg('role id doesn't exist');
        }
        $role = $this->roleModel->getById([
            'id [Int]',
            'name',
            'has_all_rule [Int]',
            'lock [Int]',
            'created_at',
            'updated_at',
        ], $roleId);
        if (empty($role)) {
            return ResultGenerator::errorWithMsg('role doesn't exist');
        }
        $ruleList = $this->roleModel->getRuleById($roleId);
        if (!empty($ruleList) && $ruleList[0]['id'] === null) {
            $ruleList = [];
        }
        return ResultGenerator::successWithData([
            'role' => $role,
            'ruleList' => $ruleList,
        ]);
    }

    public function update()
    {
        $role = $this->request->get('role');
        if (empty($role)) {
            return ResultGenerator::errorWithMsg('role doesn't exist');
        }
        $ruleList = $this->request->get('ruleList');

        $this->roleModel->updateById($role, $role['id']);
        $this->roleRuleModel->updateRuleByRoleId($ruleList, $role['id']);
        return ResultGenerator::success();
    }

    public function updateMemberRole()
    {
        $memberId = intval($this->request->get('memberId'));
        $role = $this->request->get('role');
        if (empty($memberId) || empty($role)) {
            return ResultGenerator::errorWithMsg('member id or role id doesn't exist');
        }
        $memberRoleModel = new MemberRoleModel();
        $memberRoleModel->updateBy(
            ['role_id' => $role['id']],
            ['member_id' => $memberId]
        );
        return ResultGenerator::success();
    }

    public function delete()
    {
        $roleId = intval($this->request->get('roleId'));
        if (empty($roleId)) {
            return ResultGenerator::errorWithMsg('role id doesn't exist');
        }
        $this->roleModel->deleteById($roleId);
        return ResultGenerator::success();
    }
}
