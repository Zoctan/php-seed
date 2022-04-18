<?php

namespace App\Controller;

use App\Model\LogModel;
use App\Model\RuleModel;
use App\Core\BaseController;
use App\Core\Result\Result;

/**
 * RuleController
 */
class RuleController extends BaseController
{
    /**
     * @var RuleModel
     */
    private $ruleModel;
    /**
     * @var LogModel
     */
    private $logModel;

    public function __construct(RuleModel $ruleModel, LogModel $logModel)
    {
        parent::__construct();
        $this->ruleModel = $ruleModel;
        $this->logModel = $logModel;
    }

    /**
     * List rule
     * 
     * @return Result
     */
    public function list()
    {
        $ruleList = [];
        $this->ruleModel->select(
            $this->ruleModel->getColumns(),
            function ($rule) use (&$ruleList) {
                $ruleList[] = $rule;
            }
        );
        return Result::success($ruleList);
    }

    /**
     * Add rule
     * 
     * @param int parent_id
     * @param string description
     * @param string permission
     * @return Result
     */
    public function add()
    {
        $parentId = intval($this->request->get('parent_id', 0));
        $description = strval($this->request->get('description'));
        $permission = strval($this->request->get('permission'));
        if (empty($description) || empty($permission)) {
            return Result::error('Description or permission does not exist.');
        }

        $ruleId = $this->ruleModel->insert([
            'parent_id' => $parentId,
            'description' => $description,
            'permission' => $permission,
        ]);
        $this->logModel->asInfo(sprintf('Add [id:%d][permission:%s] rule.', $ruleId, $permission));

        return Result::success($ruleId);
    }

    /**
     * Update rule list
     * 
     * @param array ruleList
     * @return Result
     */
    public function updateList()
    {
        $ruleList = (array) $this->request->get('ruleList');
        if (empty($ruleList)) {
            return Result::error('RuleList does not exist.');
        }
        foreach ($ruleList as $rule) {
            $this->ruleModel->updateById($rule, $rule['id']);
        }
        $this->logModel->asInfo(sprintf('Update rule list: %s.', json_encode($ruleList)));
        return Result::success();
    }

    /**
     * Update rule
     * 
     * @param int id
     * @param string description
     * @param string permission
     * @return Result
     */
    public function update()
    {
        $ruleId = intval($this->request->get('id'));
        $description = strval($this->request->get('description'));
        $permission = strval($this->request->get('permission'));
        if (empty($ruleId)) {
            return Result::error('Rule id does not exist.');
        }
        if (empty($description) && empty($permission)) {
            return Result::error('Description and permission does not exist.');
        }

        $this->ruleModel->updateById([
            'description' => $description,
            'permission' => $permission,
        ], $ruleId);
        $this->logModel->asInfo(sprintf('Update [id:%d][permission:%s] rule.', $ruleId, $permission));
        return Result::success();
    }

    /**
     * Delete rule list by rule id list
     * 
     * @param array ruleIdList
     * @return Result
     */
    public function deleteList()
    {
        $ruleIdList = (array) $this->request->get('ruleIdList');
        if (empty($ruleIdList)) {
            return Result::error('Rule id list does not exist.');
        }
        foreach ($ruleIdList as $id) {
            $this->ruleModel->deleteById($id);
        }
        $this->logModel->asInfo(sprintf('Delete rule list by id list: %s.', json_encode($ruleIdList)));
        return Result::success();
    }

    /**
     * Delete rule by id
     * 
     * @param int id
     * @return Result
     */
    public function delete()
    {
        $ruleId = intval($this->request->get('id'));
        if (empty($ruleId)) {
            return Result::error('Rule id does not exist.');
        }
        $this->ruleModel->deleteById($ruleId);
        $this->logModel->asInfo(sprintf('Delete [id:%d] rule.', $ruleId));
        return Result::success();
    }
}
