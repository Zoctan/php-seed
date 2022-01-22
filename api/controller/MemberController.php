<?php

namespace App\Controller;

use App\Util\JwtUtil;
use App\Model\MemberModel;
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

        $this->response->setDebug("username", $username);
        $this->response->setDebug("password", $password);

        if (empty($username) || empty($password)) {
            return ResultGenerator::errorWithMsg("请输入账户名和密码");
        }

        $memberModel = new MemberModel();
        $member = $memberModel->getByUsername($username);

        $this->response->setDebug("member", $member);
        if (empty($member)) {
            return ResultGenerator::errorWithMsg("账户名错误");
        }

        if (!$memberModel->verifyPassword($password, $member["password"])) {
            return ResultGenerator::errorWithMsg("密码错误");
        }

        if ($member["status"] == 0) {
            return ResultGenerator::errorWithMsg("成员状态异常");
        }

        $memberModel->updateLoginTimeById($member["id"]);

        $role = $memberModel->getRole($member["id"]);
        $rules = $memberModel->getRule($member["id"]);
        $operate = $memberModel->getOperate($rules);
        
        $this->response->setDebug("role", $role);
        $this->response->setDebug("rules", $rules);
        $this->response->setDebug("operate", $operate);

        $token = JwtUtil::getInstance()->sign($member["id"], ["role" => $role["name"], "operate" => $operate]);

        $this->response->setDebug("validateToken", JwtUtil::getInstance()->validateToken($token));
        return ResultGenerator::successWithData($token);
    }
}
