<?php

namespace App\Controller;

use App\Util\Util;
use App\Util\JwtUtil;
use App\Model\MemberModel;
use App\Model\MemberDataModel;
use App\Model\MemberRoleModel;
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
     * @var JwtUtil
     */
    private $jwtUtil;

    public function __construct(MemberModel $memberModel)
    {
        parent::__construct();
        $this->memberModel = $memberModel;
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

        $this->memberModel->updateLoginedAtById($memberId);

        $result = $this->jwtUtil->sign($memberId);

        return ResultGenerator::successWithData($result);
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

        $result = $this->jwtUtil->sign($member["id"]);

        return ResultGenerator::successWithData($result);
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
        $result = $this->jwtUtil->sign($this->authMember->member["id"]);
        return ResultGenerator::successWithData($result);
    }

    /**
     * 成员信息
     */
    public function detail()
    {
        $memberId = intval($this->request->get("memberId"));
        if (empty($memberId)) {
            return ResultGenerator::errorWithMsg("member id doesn't exist");
        }

        $member = $this->memberModel->getById([
            "member.id [Int]",
            "member.username",
            "member.status [Int]",
            "member.lock [Int]",
            "member.logined_at",
            "member.created_at",
            "member.updated_at",
        ], $memberId);
        if (empty($member)) {
            return ResultGenerator::errorWithMsg("member doesn't exist");
        }

        $memberDataModel = new MemberDataModel();
        $memberData = $memberDataModel->getById([
            "member_data.avatar",
            "member_data.nickname",
            "member_data.gender [Int]",
        ], $memberId);

        $memberRoleModel = new MemberRoleModel();
        $memberRole = $memberRoleModel->getRole($memberId);
        return ResultGenerator::successWithData([
            "member" => $member,
            "memberData" => $memberData,
            "role" => $memberRole,
        ]);
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

        $conditionList = [];
        // 先查询其他表，根据其他表的结果再查主表
        $memberDataList = [];
        if ($memberData) {
            $memberDataWhere = [];
            if (!empty($memberData["nickname"])) {
                $memberDataWhere["nickname[~]"] = $memberData["nickname"];
            }
            if (is_numeric($memberData["gender"])) {
                $memberDataWhere["gender"] = $memberData["gender"];
            }

            if ($memberDataWhere) {
                $memberDataModel = new MemberDataModel();
                $memberDataModel->listBy(
                    [
                        "member_id [Int]",
                    ],
                    $memberDataWhere,
                    function ($_memberData) use (&$memberDataList) {
                        $memberDataList[] = $_memberData;
                    }
                );
                array_push($conditionList, Util::value2Array($memberDataList, "member_id"));
            }
        }
        $memberRoleList = [];
        if ($role) {
            $memberRoleWhere = [];
            if (is_numeric($role["id"])) {
                $memberRoleWhere["role_id"] = $role["id"];
            }

            if ($memberRoleWhere) {
                $memberRoleModel = new MemberRoleModel();
                $memberRoleModel->listBy(
                    [
                        "member_id [Int]",
                    ],
                    $memberRoleWhere,
                    function ($_memberRole) use (&$memberRoleList) {
                        $memberRoleList[] = $_memberRole;
                    }
                );
                array_push($conditionList, Util::value2Array($memberRoleList, "member_id"));
            }
        }
        $memberList = [];
        if ($member) {
            $memberWhere = [];
            if (!empty($member["username"])) {
                $memberWhere["username[~]"] = $member["username"];
            }
            if (is_numeric($member["status"])) {
                $memberWhere["status"] = $member["status"];
            }

            if ($memberWhere) {
                $this->memberModel->listBy(
                    [
                        "id (member_id) [Int]",
                    ],
                    $memberWhere,
                    function ($_member) use (&$memberList) {
                        $memberList[] = $_member;
                    }
                );
                array_push($conditionList, Util::value2Array($memberList, "member_id"));
            }
        }
        // 数组所有可能的子集，并对这些子集做相交运算，再做分页查询
        $memberPageWhere = [];
        if (!empty($conditionList)) {
            $memberPageWhere = ["member.id" => Util::subsetsIntersect($conditionList)];
        }
        $result =  $this->memberModel->pageJoin(
            $currentPage,
            $pageSize,
            [
                "[>]member_data" => ["member.id" => "member_id"],
                "[>]member_role" => ["member_data.member_id" => "member_id"],
                "[>]role" => ["member_role.role_id" => "id"],
            ],
            [
                "member" => [
                    "member.id (member_id) [Int]",
                    "member.username",
                    "member.status [Int]",
                    "member.lock [Int]",
                    "member.logined_at",
                    "member.created_at",
                    "member.updated_at",
                ],
                "memberData" => [
                    "member_data.avatar",
                    "member_data.nickname",
                    "member_data.gender [Int]",
                ],
                "role" => [
                    "role.id [Int]",
                    "role.name",
                ],
            ],
            $memberPageWhere
        );

        return ResultGenerator::successWithData($result);
    }

    /**
     * 更新密码
     */
    public function updatePassword()
    {
        $password = strval($this->request->get("password"));
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
        $member = $this->request->get("member");
        $memberData = $this->request->get("memberData");
        if (empty($member) || empty($memberData)) {
            return ResultGenerator::errorWithMsg("member or memberData doesn't exist");
        }
        if (!empty($member)) {
            $this->memberModel->updateByMemberId($member);
        }
        if (!empty($memberData)) {
            $memberDataModel = new MemberDataModel();
            $memberDataModel->updateBy($memberData, ["member_id" => $member["id"]]);
        }
        return ResultGenerator::success();
    }

    /**
     * 添加
     */
    public function add()
    {
        $member = $this->request->get("member");
        $memberData = $this->request->get("memberData");
        $role = $this->request->get("role");
        if (empty($member)) {
            return ResultGenerator::errorWithMsg("member doesn't exist");
        }
        $memberId = $this->memberModel->add($member);
        if (!empty($memberData)) {
            $memberDataModel = new MemberDataModel();
            $memberDataModel->updateBy($memberData, ["member_id" => $memberId]);
        }
        if (!empty($role)) {
            $memberRoleModel = new MemberRoleModel();
            $memberRoleModel->updateBy(
                ["role_id" => $role["id"]],
                ["member_id" => $memberId]
            );
        }
        return ResultGenerator::successWithData($memberId);
    }

    /**
     * 删除
     */
    public function delete()
    {
        $memberId = intval($this->request->get("memberId"));
        if (empty($memberId)) {
            return ResultGenerator::errorWithMsg("member id doesn't exist");
        }
        $this->memberModel->deleteByMemberId($memberId);
        return ResultGenerator::success();
    }
}
