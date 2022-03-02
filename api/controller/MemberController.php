<?php

namespace App\Controller;

use App\Util\JwtUtil;
use App\Model\MemberModel;
use App\Model\MemberDataModel;
use App\Core\BaseController;
use App\Core\Response\ResultGenerator;

/**
 * 成员控制器
 */
class MemberController extends BaseController
{
    /**
     * @var MemberModel
     */
    private $memberModel;
    /**
     * @var MemberDataModel
     */
    private $memberDataModel;
    /**
     * @var JwtUtil
     */
    private $jwtUtil;

    public function __construct(MemberModel $memberModel, MemberDataModel $memberDataModel)
    {
        parent::__construct();
        $this->memberModel = $memberModel;
        $this->memberDataModel = $memberDataModel;
        $this->jwtUtil = JwtUtil::getInstance();
    }

    /**
     * 检查成员是否已存在
     */
    public function checkExist()
    {
        $username = strval($this->request->get("username"));

        $existMember = $this->memberModel->count(["username" => $username]) === 1;
        if ($existMember) {
            return ResultGenerator::errorWithMsg("用户名已存在");
        }

        // 其他的唯一属性
        // ...

        return ResultGenerator::success();
    }

    /**
     * 注册
     */
    public function register()
    {
        $username = strval($this->request->get("username"));
        $password = strval($this->request->get("password"));

        if (empty($username) || empty($password)) {
            return ResultGenerator::errorWithMsg("请输入用户名和密码");
        }

        $memberId = $this->memberModel->add([
            "username" => $username,
            "password" => $password,
        ]);

        $token = $this->jwtUtil->sign($memberId);

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

        $member = $this->memberModel->getByUsername($username);

        $this->response->setDebug("member", $member);
        if (empty($member)) {
            return ResultGenerator::errorWithMsg("账户名错误");
        }

        if (!$this->memberModel->verifyPassword($password, $member["password"])) {
            return ResultGenerator::errorWithMsg("密码错误");
        }

        if ($member["status"] == 0) {
            return ResultGenerator::errorWithMsg("成员状态异常");
        }

        $this->memberModel->updateLoginedAtById($member["id"]);

        $token = $this->jwtUtil->sign($member["id"]);

        return ResultGenerator::successWithData($token);
    }

    /**
     * 登出
     */
    public function logout()
    {
        $memberId = \App\DI()->authMember->memberData["member_id"];
        $this->jwtUtil->invalidRedisToken($memberId);
        return ResultGenerator::success();
    }

    /**
     * 刷新 token
     */
    public function refreshToken()
    {
        $token = $this->jwtUtil->sign($this->authMember->memberData["member_id"]);
        return ResultGenerator::successWithData($token);
    }

    /**
     * 成员信息
     */
    public function detail()
    {
        $memberId = intval($this->request->get("memberId", 0));
        $memberData = $this->memberDataModel->getById("*", $memberId);
        if (empty($memberData)) {
            return ResultGenerator::errorWithMsg("成员信息不存在");
        }
        return ResultGenerator::successWithData($memberData);
    }

    /**
     * 个人信息
     */
    public function profile()
    {
        $authMember = \App\DI()->authMember;
        return ResultGenerator::successWithData($authMember);
    }

    /**
     * 列表
     */
    public function list()
    {
        $currentPage = intval($this->request->get("currentPage", 0));
        $pageSize = intval($this->request->get("pageSize", 20));

        $username = strval($this->request->get("username"));
        $status = intval($this->request->get("status"));

        $where = [];
        if ($username) {
            $where["username[~]"] = $username;
        }
        if ($status) {
            $where["status"] = $status;
        }

        $result =  $this->memberModel->page($currentPage, $pageSize, [
            "id [Int]",
            "username",
            "status [Int]",
            "logined_at",
            "created_at",
            "updated_at",
        ], $where);
        return ResultGenerator::successWithData($result);
    }

    /**
     * 更新个人信息
     */
    public function updateProfile()
    {
        $profile = $this->request->get("profile");
        $memberId = \App\DI()->authMember->memberData["member_id"];
        $this->memberModel->updateById($profile, $memberId);
        return ResultGenerator::success();
    }

    /**
     * 更新
     */
    public function updateDetail()
    {
        $detail = $this->request->get("detail");
        $this->memberModel->updateById($detail, $detail->id);
        return ResultGenerator::success();
    }

    /**
     * 删除
     */
    public function delete()
    {
        $memberId = intval($this->request->get("memberId", 0));
        $this->memberModel->deleteById($memberId);
        return ResultGenerator::success();
    }
}
