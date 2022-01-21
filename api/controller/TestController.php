<?php

namespace App\Controller;

use App\Util\JssdkUtil;
use App\Core\BaseController;
use App\Core\Response\ResultGenerator;

class TestController extends BaseController
{
    public function show()
    {
        $id = intval($this->request->get('id'));
        if (empty($id)) {
            echo '请指定要访问的文章 ID';
            exit();
        }
        $this->response->setDebug("8", "2");
        $this->response->setDebug("2", "1");
        return ResultGenerator::success("123", ['1', '2']);
    }

    public function jssdk() {
        return ResultGenerator::success(JssdkUtil::getInstance()->getSignPackage());
    }
}
