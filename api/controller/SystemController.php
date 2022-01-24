<?php

namespace App\Controller;

use App\Model\SystemModel;
use App\Core\BaseController;
use App\Core\Response\ResultGenerator;

class SystemController extends BaseController
{
    /**
     * 获取值
     */
    public function getValue()
    {
        $key = strval($this->request->get("key"));
        $systemModel = new SystemModel();
        $result = $systemModel->getValue($key);
        return ResultGenerator::successWithData($result);
    }

    /**
     * 添加
     */
    public function add()
    {
        $description = strval($this->request->get("description"));
        $key = strval($this->request->get("key"));
        $value = strval($this->request->get("value"));

        $systemModel = new SystemModel();
        $systemModel->add($description, $key, $value);
        return ResultGenerator::success();
    }

    /*
     * 更新
     */
    public function update()
    {
        $id = intval($this->request->get("id"));
        $description = strval($this->request->get("description"));
        $key = strval($this->request->get("key"));
        $value = strval($this->request->get("value"));

        $systemModel = new SystemModel();
        $systemModel->updateById($description, $key, $value, $id);
        return ResultGenerator::success();
    }

    /*
     * 删除
     */
    public function delete()
    {
        $id = intval($this->request->get("id"));
        $systemModel = new SystemModel();
        $systemModel->deleteById($id);
        return ResultGenerator::success();
    }
}
