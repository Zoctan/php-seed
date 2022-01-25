<?php

namespace App\Controller;

use App\Util\JwtUtil;
use App\Model\AuthMemberModel;
use App\Model\MemberModel;
use App\Core\BaseController;
use App\Core\Response\ResultGenerator;

class MemberController extends BaseController
{

    /**
     * 注册
     */
    public function register()
    {
        $username = strval($this->request->get("username"));
        $password = strval($this->request->get("password"));

        if (empty($username) || empty($password)) {
            return ResultGenerator::errorWithMsg("请输入账户名和密码");
        }

        $memberModel = new MemberModel();
        $memberId = $memberModel->add([
            "username" => $username,
            "password" => $password,
        ]);

        $authMemberModel = new AuthMemberModel();
        $authMember = $authMemberModel->get($memberId);

        $token = JwtUtil::getInstance()->sign($memberId, ["role" => $authMember->role, "operate" => $authMember->operate]);

        return ResultGenerator::successWithData($token);
    }

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

        $memberModel->updateLoginedAtById($member["id"]);

        $authMemberModel = new AuthMemberModel();
        $authMember = $authMemberModel->get($member["id"]);

        $token = JwtUtil::getInstance()->sign($member["id"], ["role" => $authMember->role, "operate" => $authMember->operate]);

        return ResultGenerator::successWithData($token);
    }

    /**
     * 登出
     */
    public function logout()
    {
        $memberId = \App\DI()->authMember->member->id;
        JwtUtil::getInstance()->invalidRedisToken($memberId);
        return ResultGenerator::success();
    }

    /**
     * 更新
     */
    public function update()
    {
        $memberInfo = $this->request->get("memberInfo");
        $memberId = \App\DI()->authMember->member->id;
        $memberModel = new MemberModel();
        $memberModel->updateById($memberInfo, $memberId);
        return ResultGenerator::success();
    }

    /**
     * 删除
     */
    public function delete()
    {
        $memberId = \App\DI()->authMember->member->id;
        $memberModel = new MemberModel();
        $memberModel->deleteById($memberId);
        return ResultGenerator::success();
    }
}
