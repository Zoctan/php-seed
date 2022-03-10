<?php

namespace App\Controller;

use App\Util\JwtUtil;
use App\Model\MemberModel;
use App\Model\MemberDataModel;
use App\Model\RoleModel;
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
     * @var RoleModel
     */
    private $roleModel;
    /**
     * @var JwtUtil
     */
    private $jwtUtil;

    public function __construct(MemberModel $memberModel, MemberDataModel $memberDataModel, RoleModel $roleModel)
    {
        parent::__construct();
        $this->memberModel = $memberModel;
        $this->memberDataModel = $memberDataModel;
        $this->roleModel = $roleModel;
        $this->jwtUtil = JwtUtil::getInstance();
    }

    /**
     * 检查成员是否已存在
     */
    public function checkExist()
    {
        $username = strval($this->request->get("username"));

        if (empty($username)) {
            return ResultGenerator::errorWithMsg("please input username");
        }

        $existMember = $this->memberModel->count(["username" => $username]) === 1;
        if ($existMember) {
            return ResultGenerator::errorWithMsg("username already exists");
        }

        // 其他的唯一属性
        // ...

        return ResultGenerator::success();
    }

    /**
     * 检查旧密码是否正确
     */
    public function checkOldPassword()
    {
        $oldPassword = strval($this->request->get("oldPassword"));

        if (empty($oldPassword)) {
            return ResultGenerator::errorWithMsg("please input old password");
        }

        $memberId = $this->authMember->member["id"];
        $member = $this->memberModel->getById(["password"], $memberId);

        if (!$this->memberModel->verifyPassword($oldPassword, $member["password"])) {
            return ResultGenerator::errorWithMsg("old password error");
        }

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
            return ResultGenerator::errorWithMsg("please input username and password");
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
            return ResultGenerator::errorWithMsg("please input username and password");
        }

        $member = $this->memberModel->getByUsername($username);

        $this->response->setDebug("member", $member);
        if (empty($member)) {
            return ResultGenerator::errorWithMsg("username error");
        }

        if (!$this->memberModel->verifyPassword($password, $member["password"])) {
            return ResultGenerator::errorWithMsg("password error");
        }

        if ($member["status"] == 0) {
            return ResultGenerator::errorWithMsg("member status error");
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
        $memberId = $this->authMember->member["id"];
        $this->jwtUtil->invalidRedisToken($memberId);
        return ResultGenerator::success();
    }

    /**
     * 刷新 token
     */
    public function refreshToken()
    {
        $token = $this->jwtUtil->sign($this->authMember->member["id"]);
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
            return ResultGenerator::errorWithMsg("member data doesn't exist");
        }
        return ResultGenerator::successWithData($memberData);
    }

    /**
     * 个人信息
     */
    public function profile()
    {
        $authMember = $this->authMember;
        return ResultGenerator::successWithData($authMember);
    }

    /**
     * 列表
     */
    public function list()
    {
        $currentPage = intval($this->request->get("currentPage", 0));
        $pageSize = intval($this->request->get("pageSize", 20));

        $member = $this->request->get("member");
        $memberData = $this->request->get("memberData");
        $role = $this->request->get("role");

        // 先查询其他表，根据其他表的结果再查主表
        $memberDataList = [];
        if ($memberData) {
            $memberDataWhere = [];
            if ($memberData["nickname"]) {
                $memberDataWhere["nickname[~]"] = $memberData["nickname"];
            }
            if ($memberData["gender"]) {
                $memberDataWhere["gender"] = $memberData["gender"];
            }

            if ($memberDataWhere) {
                $this->memberDataModel->listBy(
                    [
                        "id [Int]",
                        "member_id [Int]",
                        "avatar",
                        "nickname",
                        "gender [Int]",
                        "created_at",
                        "updated_at",
                    ],
                    $memberDataWhere,
                    function ($_memberData) use (&$memberDataList) {
                        $memberDataList[] = $_memberData;
                    }
                );
            }
        }
        $roleList = [];
        if ($role) {
            $roleWhere = [];
            if ($role["name"]) {
                $roleWhere["name[~]"] = $role["name"];
            }

            if ($roleWhere) {
                $this->roleModel->listBy(
                    [
                        "id [Int]",
                        "name",
                        "has_all_rule [Int]",
                        "lock [Int]",
                        "created_at",
                        "updated_at",
                    ],
                    $roleWhere,
                    function ($_role) use (&$roleList) {
                        $roleList[] = $_role;
                    }
                );
            }
        }
        $memberWhere = [];
        if ($member) {
            if ($member["username"]) {
                $memberWhere["username[~]"] = $member["username"];
            }
            if ($member["status"]) {
                $memberWhere["status"] = $member["status"];
            }
        }
        // todo
        $result =  $this->memberModel->page($currentPage, $pageSize, [
            "id [Int]",
            "username",
            "status [Int]",
            "logined_at",
            "created_at",
            "updated_at",
        ], $memberWhere);

        return ResultGenerator::successWithData($result);
    }

    /**
     * 更新密码
     */
    public function updatePassword()
    {
        $password = $this->request->get("password");
        $memberId = $this->authMember->member["id"];
        $this->memberModel->updateById(["password" => $password], $memberId);
        return ResultGenerator::success();
    }

    /**
     * 更新个人信息
     */
    public function updateProfile()
    {
        $profile = $this->request->get("profile");
        $memberId = $this->authMember->member["id"];
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
