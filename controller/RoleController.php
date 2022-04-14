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
            return ResultGenerator::errorWithMsg('role does not exist');
        }

        $roleId = $this->roleModel->insert($role);

        $this->roleRuleModel->updateRuleByRoleId($ruleList, $roleId);
        return ResultGenerator::successWithData($roleId);
    }

    public function list()
    {
        $currentPage = intval($this->request->get('currentPage', 0));
        $pageSize = intval($this->request->get('pageSize', 0));

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
            $this->roleModel->getColumns(),
            $where
        );
        return ResultGenerator::successWithData($result);
    }

    public function detail()
    {
        $roleId = intval($this->request->get('roleId'));
        if (empty($roleId)) {
            return ResultGenerator::errorWithMsg('role id does not exist');
        }
        $role = $this->roleModel->getById($this->roleModel->getColumns(), $roleId);
        if (empty($role)) {
            return ResultGenerator::errorWithMsg('role does not exist');
        }
        $ruleList = $this->roleModel->listRuleByRoleId($roleId);
        if (!empty($ruleList) && $ruleList[0]['id'] === null) {
            $ruleList = [];
        }
        return ResultGenerator::successWithData([
            'role' => $role,
            'ruleList' => $ruleList,
        ]);
    }

    public function listParent()
    {
        $parentId = intval($this->request->get('parentId'));
        if (empty($parentId)) {
            return ResultGenerator::errorWithMsg('parent id does not exist');
        }
        $parentList = $this->roleModel->listParentByParentId($parentId, $this->roleModel->getColumns());
        return ResultGenerator::successWithData($parentList);
    }

    public function update()
    {
        $role = $this->request->get('role');
        if (empty($role)) {
            return ResultGenerator::errorWithMsg('role does not exist');
        }
        $ruleList = $this->request->get('ruleList');

        $this->roleModel->updateById($role, $role['id']);
        $this->roleRuleModel->updateRuleByRoleId($ruleList, $role['id']);
        return ResultGenerator::success();
    }

    public function delete()
    {
        $roleId = intval($this->request->get('roleId'));
        if (empty($roleId)) {
            return ResultGenerator::errorWithMsg('role id does not exist');
        }
        $this->roleModel->deleteById($roleId);
        return ResultGenerator::success();
    }

    public function addMemberRole()
    {
        $memberId = intval($this->request->get('memberId'));
        $roleId = intval($this->request->get('roleId'));
        if (empty($memberId) || empty($roleId)) {
            return ResultGenerator::errorWithMsg('member id or role id does not exist');
        }
        $memberRoleModel = new MemberRoleModel();
        $memberRoleModel->insert(['member_id' => $memberId, 'role_id' => $roleId]);
        $role = $this->roleModel->getById($this->roleModel->getColumns(), $roleId);
        return ResultGenerator::successWithData($role);
    }

    public function deleteMemberRole()
    {
        $memberId = intval($this->request->get('memberId'));
        $roleId = intval($this->request->get('roleId'));
        if (empty($memberId) || empty($roleId)) {
            return ResultGenerator::errorWithMsg('member id or role id does not exist');
        }
        $memberRoleModel = new MemberRoleModel();
        $memberRoleModel->deleteByMember_idRole_id([$memberId, $roleId]);
        return ResultGenerator::success();
    }
}
