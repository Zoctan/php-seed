<?php

namespace App\Controller;

use App\Model\LogModel;
use App\Core\BaseController;
use App\Core\Response\ResultGenerator;

class LogController extends BaseController
{
    /**
     * 获取
     */
    public function list()
    {
        $key = strval($this->request->get("key"));
        $logModel = new LogModel();
        $result = $logModel->list($key);
        return ResultGenerator::successWithData($result);
    }

    /*
     * 删除
     */
    public function delete()
    {
        $id = intval($this->request->get("id"));
        $logModel = new LogModel();
        $logModel->deleteById($id);
        return ResultGenerator::success();
    }
}
