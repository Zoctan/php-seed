<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Model\MemberModel;
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

        $this->response->setDebug("username", "username");
        $this->response->setDebug("password", "password");

        if (empty($username) || empty($password)) {
            return ResultGenerator::errorWithMsg("请输入账户名和密码");
        }

        $memberModel = new MemberModel();
        $member = $memberModel->getByUsername($username);

        if (empty($member)) {
            return ResultGenerator::errorWithMsg("账户名错误");
        }

        if ($memberModel->verifyPassword($password, $member->password)) {
            return ResultGenerator::errorWithMsg("密码错误");
        }

        if ($member->status == 0) {
            return ResultGenerator::errorWithMsg("成员状态异常");
        }
        
        $memberModel->updateLoginTimeByName($username);

        $this->response->setDebug("token", "username");
        return ResultGenerator::successWithData();
    }
}
