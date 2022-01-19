<?php

namespace PHPSeed\Controller;

use PHPSeed\Core\BaseController;
use PHPSeed\Core\Response\ResultGenerator;
use PHPSeed\Core\Response\ResultCode;

class TestController extends BaseController
{
    public function show()
    {
        $id = intval($this->request->get('id'));
        if (empty($id)) {
            echo '请指定要访问的文章 ID';
            exit();
        }

        return ResultGenerator::success("123", ['1', '2']);
    }
}
