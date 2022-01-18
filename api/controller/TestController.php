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

        var_dump(ResultGenerator::success("123", ['1','2']));
        var_dump(ResultGenerator::error(ResultCode::FIND_FAILED, "456"));
        // var_dump(ResultCode::SUCCEED[1]);
    }
}
