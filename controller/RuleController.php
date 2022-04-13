<?php

namespace App\Controller;

use App\Util\Util;
use App\Model\RuleModel;
use App\Core\BaseController;
use App\Core\Response\ResultGenerator;

class RuleController extends BaseController
{
    /**
     * @var RuleModel
     */
    private $ruleModel;

    public function __construct(RuleModel $ruleModel)
    {
        parent::__construct();
        $this->ruleModel = $ruleModel;
    }

    public function add()
    {
        $parentId = intval($this->request->get('parent_id', 0));
        $description = strval($this->request->get('description'));
        $permission = strval($this->request->get('permission'));

        if (empty($description) || empty($permission)) {
            return ResultGenerator::errorWithMsg('please input description and permission');
        }

        $ruleId = $this->ruleModel->insert([
            'parent_id' => $parentId,
            'description' => $description,
            'permission' => $permission,
        ]);

        return ResultGenerator::successWithData($ruleId);
    }

    public function list()
    {
        $ruleList = [];
        $this->ruleModel->select(
            $this->ruleModel->getColumns(),
            function ($rule) use (&$ruleList) {
                $ruleList[] = $rule;
            }
        );
        return ResultGenerator::successWithData($ruleList);
    }

    public function updateList()
    {
        $ruleList = (array) $this->request->get('ruleList');
        if (empty($ruleList)) {
            return ResultGenerator::errorWithMsg('ruleList does not exist');
        }
        foreach ($ruleList as $rule) {
            $this->ruleModel->updateById($rule, $rule['id']);
        }
        return ResultGenerator::success();
    }

    public function update()
    {
        $ruleId = intval($this->request->get('id'));
        $description = strval($this->request->get('description'));
        $permission = strval($this->request->get('permission'));
        if (empty($ruleId)) {
            return ResultGenerator::errorWithMsg('rule id does not exist');
        }
        if (empty($description) && empty($permission)) {
            return ResultGenerator::errorWithMsg('please input description or permission');
        }

        $this->ruleModel->updateById([
            'description' => $description,
            'permission' => $permission,
        ], $ruleId);
        return ResultGenerator::success();
    }

    public function deleteList()
    {
        $ruleIdList = $this->request->get('ruleIdList');
        if (empty($ruleIdList)) {
            return ResultGenerator::errorWithMsg('ruleIdList does not exist');
        }
        foreach ($ruleIdList as $id) {
            $this->ruleModel->deleteById($id);
        }
        return ResultGenerator::success();
    }

    public function delete()
    {
        $ruleId = intval($this->request->get('ruleId'));
        if (empty($ruleId)) {
            return ResultGenerator::errorWithMsg('rule id does not exist');
        }
        $this->ruleModel->deleteById($ruleId);
        return ResultGenerator::success();
    }
}
