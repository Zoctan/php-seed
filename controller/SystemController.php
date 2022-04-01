<?php

namespace App\Controller;

use App\Model\SystemModel;
use App\Core\BaseController;
use App\Core\Response\ResultGenerator;

class SystemController extends BaseController
{
    /**
     * @var SystemModel
     */
    private $systemModel;

    public function __construct(SystemModel $systemModel)
    {
        parent::__construct();
        $this->systemModel = $systemModel;
    }

    /**
     * 获取值
     */
    public function getValue()
    {
        $key = strval($this->request->get('key'));
        if (empty($key)) {
            return ResultGenerator::errorWithMsg('key does not exist');
        }
        $result = $this->systemModel->getValue($key);
        return ResultGenerator::successWithData($result);
    }

    /**
     * 添加
     */
    public function add()
    {
        $system = $this->request->get('system');
        if (empty($system)) {
            return ResultGenerator::errorWithMsg('system does not exist');
        }
        $this->systemModel->insert($system);
        return ResultGenerator::success();
    }

    /*
     * 更新
     */
    public function update()
    {
        $system = $this->request->get('system');
        if (empty($system)) {
            return ResultGenerator::errorWithMsg('system does not exist');
        }
        $this->systemModel->updateById($system, $system['id']);
        return ResultGenerator::success();
    }

    /*
     * 删除
     */
    public function delete()
    {
        $systemId = intval($this->request->get('systemId'));
        if (empty($systemId)) {
            return ResultGenerator::errorWithMsg('system id does not exist');
        }
        $this->systemModel->deleteById($systemId);
        return ResultGenerator::success();
    }
}
