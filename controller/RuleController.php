<?php

namespace App\Controller;

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

    public function __construct(RuleModel $ruleModel)
    {
        parent::__construct();
        $this->ruleModel = $ruleModel;
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
            return Result::error('Description or permission does not exist');
        }

        $ruleId = $this->ruleModel->insert([
            'parent_id' => $parentId,
            'description' => $description,
            'permission' => $permission,
        ]);
        \App\log()->asInfo(sprintf('Add rule: [id:%d][permission:%s]', $ruleId, $permission));

        return Result::success($ruleId);
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
            return Result::error('Rule id does not exist');
        }
        if (empty($description) && empty($permission)) {
            return Result::error('Description and permission does not exist');
        }

        $this->ruleModel->updateById([
            'description' => $description,
            'permission' => $permission,
        ], $ruleId);
        \App\log()->asInfo(sprintf('Update rule: [id:%d][permission:%s]', $ruleId, $permission));
        return Result::success();
    }

    /**
     * Remove rule list by rule id list
     * 
     * @param array ruleIdList
     * @return Result
     */
    public function removeList()
    {
        $ruleIdList = (array) $this->request->get('ruleIdList');
        if (empty($ruleIdList)) {
            return Result::error('Rule id list does not exist');
        }
        $this->ruleModel->delete(['id' => $ruleIdList]);
        \App\log()->asInfo(sprintf('Remove rule list by id list: %s', json_encode($ruleIdList)));
        return Result::success();
    }

    /**
     * Remove rule by id
     * 
     * @param int id
     * @return Result
     */
    public function remove()
    {
        $ruleId = intval($this->request->get('id'));
        if (empty($ruleId)) {
            return Result::error('Rule id does not exist');
        }
        $this->ruleModel->deleteById($ruleId);
        \App\log()->asInfo(sprintf('Remove rule: [id:%d]', $ruleId));
        return Result::success();
    }
}
