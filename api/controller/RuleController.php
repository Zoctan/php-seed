<?php

namespace App\Controller;

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

    public function list()
    {
        $ruleList = $this->ruleModel->listAllWithoutCondition();
        return ResultGenerator::successWithData($ruleList);
    }

    public function updateList()
    {
        $ruleList = $this->request->get("ruleList");
        if (empty($ruleList)) {
            return ResultGenerator::errorWithMsg("ruleList doesn't exist");
        }
        //
        return ResultGenerator::success();
    }

    public function update()
    {
        $ruleId = intval($this->request->get("ruleId"));
        $description = strval($this->request->get("description"));
        $permission = strval($this->request->get("permission"));
        if (empty($ruleId)) {
            return ResultGenerator::errorWithMsg("rule id doesn't exist");
        }
        if (empty($description) && empty($permission)) {
            return ResultGenerator::errorWithMsg("please input description or permission");
        }

        $this->ruleModel->updateById([
            "description" => $description,
            "permission" => $permission,
        ], $ruleId);
        return ResultGenerator::success();
    }

    public function delete()
    {
        $ruleId = intval($this->request->get("ruleId"));
        if (empty($ruleId)) {
            return ResultGenerator::errorWithMsg("rule id doesn't exist");
        }
        $this->ruleModel->deleteById($ruleId);
        return ResultGenerator::success();
    }
}
