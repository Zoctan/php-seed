<?php

namespace App\Controller;

use App\Model\RoleModel;
use App\Model\RoleRuleModel;
use App\Model\MemberRoleModel;
use App\Core\BaseController;
use App\Core\Result\Result;

/**
 * RoleController
 */
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

    /**
     * Add role
     * 
     * @param object role
     * @param object ruleList
     */
    public function add()
    {
        $role = $this->request->get('role');
        $ruleList = $this->request->get('ruleList');
        if (empty($role)) {
            return Result::error('Role does not exist');
        }

        $roleId = $this->roleModel->insert($role);

        $this->roleRuleModel->updateRuleByRoleId($ruleList, $roleId);
        return Result::success($roleId);
    }

    /**
     * List role
     * 
     * @param int currentPage
     * @param int pageSize
     * @param object role
     */
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
        return Result::success($result);
    }

    /**
     * Get role and rule list by id
     * 
     * @param int id
     */
    public function detail()
    {
        $roleId = intval($this->request->get('id'));
        if (empty($roleId)) {
            return Result::error('Role id does not exist');
        }
        $role = $this->roleModel->getById($this->roleModel->getColumns(), $roleId);
        if (empty($role)) {
            return Result::error('Role does not exist');
        }
        $ruleList = $this->roleModel->listRuleByRoleId($roleId);
        if (!empty($ruleList) && $ruleList[0]['id'] === null) {
            $ruleList = [];
        }
        return Result::success([
            'role' => $role,
            'ruleList' => $ruleList,
        ]);
    }

    /**
     * List parent role
     * 
     * @param int parentId
     */
    public function listParent()
    {
        $parentId = intval($this->request->get('parentId'));
        if (empty($parentId)) {
            return Result::error('Parent id does not exist');
        }
        $parentList = $this->roleModel->listParentByParentId($parentId, $this->roleModel->getColumns());
        return Result::success($parentList);
    }

    /**
     * Update role
     * 
     * @param object role
     */
    public function update()
    {
        $role = $this->request->get('role');
        if (empty($role)) {
            return Result::error('Role does not exist');
        }
        $ruleList = $this->request->get('ruleList');

        $this->roleModel->updateById($role, $role['id']);
        $this->roleRuleModel->updateRuleByRoleId($ruleList, $role['id']);
        return Result::success();
    }

    /**
     * Delete role by id
     * 
     * @param int id
     */
    public function delete()
    {
        $roleId = intval($this->request->get('id'));
        if (empty($roleId)) {
            return Result::error('Role id does not exist');
        }
        $this->roleModel->deleteById($roleId);
        return Result::success();
    }

    /**
     * Add member role
     * 
     * @param int memberId
     * @param int roleId
     */
    public function addMemberRole()
    {
        $memberId = intval($this->request->get('memberId'));
        $roleId = intval($this->request->get('roleId'));
        if (empty($memberId) || empty($roleId)) {
            return Result::error('Member id or role id does not exist');
        }
        $memberRoleModel = new MemberRoleModel();
        $memberRoleModel->insert(['member_id' => $memberId, 'role_id' => $roleId]);
        $role = $this->roleModel->getById($this->roleModel->getColumns(), $roleId);
        return Result::success($role);
    }

    /**
     * Delete member role
     * 
     * @param int memberId
     * @param int roleId
     */
    public function deleteMemberRole()
    {
        $memberId = intval($this->request->get('memberId'));
        $roleId = intval($this->request->get('roleId'));
        if (empty($memberId) || empty($roleId)) {
            return Result::error('Member id or role id does not exist');
        }
        $memberRoleModel = new MemberRoleModel();
        $memberRoleModel->deleteByMember_idRole_id([$memberId, $roleId]);
        return Result::success();
    }
}
