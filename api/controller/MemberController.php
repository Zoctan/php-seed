<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Core\Response\ResultGenerator;

class MemberController extends BaseController
{
    /**
     * 登录
     */
    public function login()
    {
        $username = strval($this->request->get("username"));
        $password = strval($this->request->get("password"));
        if (empty($username)) {
            echo "请指定要访问的文章 ID";
            exit();
        }
        $this->response->setDebug("username", "username");
        $this->response->setDebug("password", "password");
        return ResultGenerator::success("123", ["1", "2"]);
    }
}
