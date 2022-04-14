<?php

namespace App\Controller;

use App\Util\Util;
use App\Util\JwtUtil;
use App\Model\MemberModel;
use App\Model\MemberDataModel;
use App\Model\MemberRoleModel;
use App\Model\RoleModel;
use App\Core\BaseController;
use App\Core\Response\ResultCode;
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
     * is member exist
     */
    public function isMemberExist()
    {
        $username = strval($this->request->get('username'));

        if (empty($username)) {
            return ResultGenerator::errorWithMsg('please input username');
        }

        $existMember = $this->memberModel->countByUsername($username) === 1;
        if ($existMember) {
            return ResultGenerator::errorWithMsg('username already exists');
        }

        // other attributions that can find the unique member
        // ...

        return ResultGenerator::success();
    }

    /**
     * validate old password
     */
    public function validateOldPassword()
    {
        $oldPassword = strval($this->request->get('oldPassword'));

        if (empty($oldPassword)) {
            return ResultGenerator::errorWithMsg('please input old password');
        }

        $memberId = $this->authMember->member['id'];
        $member = $this->memberModel->getById(['password'], $memberId);

        if (!$this->memberModel->verifyPassword($oldPassword, $member['password'])) {
            return ResultGenerator::errorWithMsg('old password error');
        }

        return ResultGenerator::success();
    }

    /**
     * register
     */
    public function register()
    {
        $username = strval($this->request->get('username'));
        $password = strval($this->request->get('password'));

        if (empty($username) || empty($password)) {
            return ResultGenerator::errorWithMsg('please input username and password');
        }

        $memberId = $this->memberModel->add([
            'username' => $username,
            'password' => $password,
        ]);

        $this->memberModel->updateLoginedAtById($memberId);

        $result = $this->jwtUtil->sign($memberId);

        return ResultGenerator::successWithData($result);
    }

    /**
     * login
     */
    public function login()
    {
        $username = strval($this->request->get('username'));
        $password = strval($this->request->get('password'));

        if (empty($username) || empty($password)) {
            return ResultGenerator::errorWithMsg('please input username and password');
        }

        $member = $this->memberModel->getByUsername(
            $this->memberModel->getColumns(),
            $username
        );

        if (empty($member)) {
            return ResultGenerator::errorWithMsg('username error');
        }

        if (!$this->memberModel->verifyPassword($password, $member['password'])) {
            return ResultGenerator::errorWithMsg('password error');
        }

        if ($member['status'] !== 1) {
            return ResultGenerator::errorWithMsg('member status error');
        }

        $this->memberModel->updateLoginedAtById($member['id']);

        $result = $this->jwtUtil->sign($member['id']);

        return ResultGenerator::successWithData($result);
    }

    /**
     * logout
     */
    public function logout()
    {
        if ($this->authMember) {
            $memberId = $this->authMember->member['id'];
            $this->jwtUtil->invalidRedisToken($memberId);
        }
        return ResultGenerator::success();
    }

    /**
     * validate access token
     */
    public function validateAccessToken()
    {
        // read access token from header
        // if use POST or GET data, make sure the access token had been changed when refresh token
        $accessToken = $this->jwtUtil->getTokenFromRequest();
        if (empty($accessToken)) {
            return ResultGenerator::errorWithCodeMsg(ResultCode::ACCESS_TOKEN_EXCEPTION, 'accessToken does not exist');
        }
        $accessTokenValidation = $this->jwtUtil->validateTokenRedis($accessToken);
        if (!$accessTokenValidation) {
            return ResultGenerator::errorWithCodeMsg(ResultCode::ACCESS_TOKEN_EXCEPTION, 'invalid accessToken');
        }
        return ResultGenerator::successWithMsg('accessToken is valid');
    }

    /**
     * refresh access token
     */
    public function refreshToken()
    {
        // check old access token
        $oldAccessToken = $this->jwtUtil->getTokenFromRequest();
        if (empty($oldAccessToken)) {
            return ResultGenerator::errorWithCodeMsg(ResultCode::REFRESH_TOKEN_EXCEPTION, 'old accessToken does not exist');
        }
        $oldAuthMember = $this->jwtUtil->getAuthMember($oldAccessToken);
        if (empty($oldAuthMember) || empty($oldAuthMember->member['id'])) {
            return ResultGenerator::errorWithCodeMsg(ResultCode::REFRESH_TOKEN_EXCEPTION, 'old authMember does not exist, old accessToken error');
        }
        // check refresh token
        $refreshToken = strval($this->request->get('refreshToken'));
        if (empty($refreshToken)) {
            return ResultGenerator::errorWithCodeMsg(ResultCode::REFRESH_TOKEN_EXCEPTION, 'refreshToken does not exist');
        }
        if (!$this->jwtUtil->validateToken($refreshToken)) {
            return ResultGenerator::errorWithCodeMsg(ResultCode::REFRESH_TOKEN_EXCEPTION, 'invalid refreshToken');
        }
        $authMember = $this->jwtUtil->getAuthMember($refreshToken);
        if (empty($authMember) || empty($authMember->member['id'])) {
            return ResultGenerator::errorWithCodeMsg(ResultCode::REFRESH_TOKEN_EXCEPTION, 'new authMember does not exist, refreshToken error');
        }
        // is access token and refresh token from the same member
        if ($oldAuthMember->member['id'] !== $authMember->member['id']) {
            return ResultGenerator::errorWithCodeMsg(ResultCode::TOKEN_EXCEPTION, 'accessToken does not match the refreshToken');
        }
        $result = $this->jwtUtil->signAccessToken($authMember->member['id']);
        return ResultGenerator::successWithData($result);
    }

    /**
     * member detail
     */
    public function detail()
    {
        $memberId = intval($this->request->get('memberId'));
        if (empty($memberId)) {
            return ResultGenerator::errorWithMsg('member id does not exist');
        }

        $member = $this->memberModel->getById(
            $this->memberModel->getColumnsExcept(['password']),
            $memberId
        );
        if (empty($member)) {
            return ResultGenerator::errorWithMsg('id error, member does not exist');
        }

        $memberDataModel = new MemberDataModel();
        $memberData = $memberDataModel->getById(
            $memberDataModel->getColumns(),
            $memberId
        );

        $memberRoleModel = new MemberRoleModel();
        $roleList = $memberRoleModel->listRoleByMemberId($memberId);

        return ResultGenerator::successWithData([
            'member' => $member,
            'memberData' => $memberData,
            'roleList' => $roleList,
        ]);
    }

    /**
     * member profile
     */
    public function profile()
    {
        return ResultGenerator::successWithData($this->authMember);
    }

    /**
     * member list
     */
    public function list()
    {
        $currentPage = intval($this->request->get('currentPage', 0));
        $pageSize = intval($this->request->get('pageSize', 20));

        $member = $this->request->get('member');
        $memberData = $this->request->get('memberData');
        $role = $this->request->get('role');

        $memberDataModel = new MemberDataModel();
        $memberRoleModel = new MemberRoleModel();

        $conditionList = [];
        // 先查询其他表，根据其他表的结果再查主表
        $memberDataList = [];
        if ($memberData) {
            $memberDataWhere = [];
            if (!empty($memberData['nickname'])) {
                $memberDataWhere['nickname[~]'] = $memberData['nickname'];
            }
            if (is_numeric($memberData['gender'])) {
                $memberDataWhere['gender'] = $memberData['gender'];
            }

            if ($memberDataWhere) {
                $memberDataModel->select(
                    $memberDataModel->getColumns('member_id'),
                    $memberDataWhere,
                    function ($_memberData) use (&$memberDataList) {
                        $memberDataList[] = $_memberData;
                    }
                );
                array_push($conditionList, Util::getValueList('member_id', $memberDataList));
            }
        }
        $memberRoleList = [];
        if ($role) {
            $memberRoleWhere = [];
            if (is_numeric($role['id'])) {
                $memberRoleWhere['role_id'] = $role['id'];
            }

            if ($memberRoleWhere) {
                $memberRoleModel->select(
                    $memberDataModel->getColumns('member_id'),
                    $memberRoleWhere,
                    function ($_memberRole) use (&$memberRoleList) {
                        $memberRoleList[] = $_memberRole;
                    }
                );
                array_push($conditionList, Util::getValueList('member_id', $memberRoleList));
            }
        }
        $memberList = [];
        if ($member) {
            $memberWhere = [];
            if (!empty($member['username'])) {
                $memberWhere['username[~]'] = $member['username'];
            }
            if (is_numeric($member['status'])) {
                $memberWhere['status'] = $member['status'];
            }

            if ($memberWhere) {
                $this->memberModel->select(
                    $memberDataModel->getColumns('member_id'),
                    $memberWhere,
                    function ($_member) use (&$memberList) {
                        $memberList[] = $_member;
                    }
                );
                array_push($conditionList, Util::getValueList('member_id', $memberList));
            }
        }
        \App\debug('memberDataList', $memberDataList);
        \App\debug('memberRoleList', $memberRoleList);
        \App\debug('memberList', $memberList);
        // all subsets make intersect
        $memberPageWhere = [];
        if (!empty($conditionList)) {
            $memberPageWhere = ['member.id' => Util::subsetsIntersect($conditionList)];
        }
        $result = $this->memberModel->pageJoin(
            $currentPage,
            $pageSize,
            [
                '[>]member_data' => ['member.id' => 'member_id'],
            ],
            [
                'member' => [
                    'member.id (member_id) [Int]',
                    'member.username',
                    'member.status [Int]',
                    'member.lock [Int]',
                    'member.logined_at',
                    'member.created_at',
                    'member.updated_at',
                ],
                'memberData' => [
                    'member_data.avatar',
                    'member_data.nickname',
                    'member_data.gender [Int]',
                ],
            ],
            $memberPageWhere
        );
        // add role list
        for ($i = 0; $i < count($result['list']); $i++) {
            $roleList = $memberRoleModel->listRoleByMemberId($result['list'][$i]['member']['member_id']);
            $result['list'][$i]['roleList'] = $roleList;
        }

        return ResultGenerator::successWithData($result);
    }

    /**
     * update password
     */
    public function updatePassword()
    {
        $password = strval($this->request->get('password'));
        $memberId = $this->authMember->member['id'];
        $this->memberModel->updateById(['password' => $password], $memberId);
        return ResultGenerator::success();
    }

    /**
     * update profile
     */
    public function updateProfile()
    {
        $profile = $this->request->get('profile');
        $memberId = $this->authMember->member['id'];
        $this->memberModel->updateById($profile, $memberId);
        return ResultGenerator::success();
    }


    /**
     * update detail
     */
    public function updateDetail()
    {
        $member = $this->request->get('member');
        $memberData = $this->request->get('memberData');
        if (empty($member) || empty($memberData)) {
            return ResultGenerator::errorWithMsg('member or memberData does not exist');
        }
        if (!empty($member)) {
            $this->memberModel->update($member, $member['id']);
        }
        if (!empty($memberData)) {
            $memberDataModel = new MemberDataModel();
            $memberDataModel->updateByMember_id($memberData, $member['id']);
        }
        return ResultGenerator::success();
    }

    /**
     * add member
     */
    public function add()
    {
        $member = $this->request->get('member');
        $memberData = $this->request->get('memberData');
        $role = $this->request->get('role');
        if (empty($member)) {
            return ResultGenerator::errorWithMsg('member does not exist');
        }
        $memberId = $this->memberModel->add($member);
        if (!empty($memberData)) {
            $memberDataModel = new MemberDataModel();
            $memberDataModel->updateByMember_id($memberData, $memberId);
        }
        if (!empty($role)) {
            $memberRoleModel = new MemberRoleModel();
            $memberRoleModel->updateByRole_idMember_id([$role['id'], $memberId]);
        }
        return ResultGenerator::successWithData($memberId);
    }

    /**
     * delete member
     */
    public function delete()
    {
        $memberId = intval($this->request->get('memberId'));
        if (empty($memberId)) {
            return ResultGenerator::errorWithMsg('member id does not exist');
        }
        $this->memberModel->deleteById($memberId);
        $memberDataModel = new MemberDataModel();
        $memberDataModel->deleteByMember_id($memberId);

        $memberRoleModel = new MemberRoleModel();
        $memberRoleModel->deleteByMember_id($memberId);
        return ResultGenerator::success();
    }
}
