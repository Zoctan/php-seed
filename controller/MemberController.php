<?php

namespace App\Controller;

use Medoo\Medoo;
use App\Util\Util;
use App\Util\Jwt;
use App\Model\MemberModel;
use App\Model\MemberDataModel;
use App\Model\MemberRoleModel;
use App\Model\RoleModel;
use App\Core\BaseController;
use App\Core\Result\Result;
use App\Core\Result\ResultCode;

/**
 * MemberController
 */
class MemberController extends BaseController
{
    /**
     * @var MemberModel
     */
    private $memberModel;
    /**
     * @var Jwt
     */
    private $jwt;

    public function __construct(MemberModel $memberModel)
    {
        parent::__construct();
        $this->memberModel = $memberModel;
        $this->jwt = Jwt::getInstance();
    }

    /**
     * Is member exist
     * 
     * @param string username
     * @return Result
     */
    public function isMemberExist()
    {
        $username = strval($this->request->get('username'));
        if (empty($username)) {
            return Result::error('Username does not exist');
        }

        $existMember = $this->memberModel->countByUsername($username) === 1;
        if ($existMember) {
            return Result::error('Username already existed');
        }

        // other attributions that can find the unique member
        // ...

        return Result::success();
    }

    /**
     * Member register
     * 
     * @param string username
     * @param string password
     * @return Result
     */
    public function register()
    {
        $username = strval($this->request->get('username'));
        $password = strval($this->request->get('password'));
        if (empty($username) || empty($password)) {
            return Result::error('Username or password does not exist');
        }

        $memberId = $this->memberModel->add([
            'username' => $username,
            'password' => $password,
        ]);

        $this->memberModel->updateLoginedAtById($memberId);

        $result = $this->jwt->sign($memberId);
        return Result::success($result);
    }

    /**
     * Member login
     * 
     * @param string username
     * @param string password
     * @return Result
     */
    public function login()
    {
        $username = strval($this->request->get('username'));
        $password = strval($this->request->get('password'));
        if (empty($username) || empty($password)) {
            return Result::error('Username or password does not exist');
        }

        $member = $this->memberModel->getByUsername($this->memberModel->getColumns(), $username);

        if (empty($member)) {
            return Result::error('Username error');
        }
        if (!$this->memberModel->verifyPassword($password, $member['password'])) {
            \App\log()->asError('Try login, but password error');
            return Result::error('Password error');
        }
        if ($member['status'] !== 1) {
            return Result::error('Member status error');
        }

        $this->memberModel->updateLoginedAtById($member['id']);

        $result = $this->jwt->sign($member['id']);
        return Result::success($result);
    }

    /**
     * Member logout
     * 
     * @return Result
     */
    public function logout()
    {
        if ($this->authMember) {
            $memberId = $this->authMember->member['id'];
            $this->jwt->invalidRedisToken($memberId);
        }
        return Result::success();
    }

    /**
     * Get member and member data and role list by id
     * 
     * @param int id
     * @return Result
     */
    public function detail()
    {
        $memberId = intval($this->request->get('id'));
        if (empty($memberId)) {
            return Result::error('Member id does not exist');
        }

        $member = $this->memberModel->getById(
            $this->memberModel->getColumnsExcept(['password']),
            $memberId
        );
        if (empty($member)) {
            return Result::error('Id error, member does not exist');
        }

        $memberDataModel = new MemberDataModel();
        $memberData = $memberDataModel->getById($memberDataModel->getColumns(), $memberId);

        $memberRoleModel = new MemberRoleModel();
        $roleList = $memberRoleModel->listRoleByMemberId($memberId);

        return Result::success([
            'member' => $member,
            'memberData' => $memberData,
            'roleList' => $roleList,
        ]);
    }

    /**
     * Logined member profile
     * 
     * @return Result
     */
    public function profile()
    {
        return Result::success($this->authMember);
    }

    /**
     * List member
     * 
     * @param int currentPage
     * @param int pageSize
     * @param object member
     * @param object memberData
     * @param object role
     * @return Result
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
        // select addition table first, and select main table second
        $memberDataList = [];
        if ($memberData) {
            $memberDataWhere = [];
            if (isset($memberData['nickname'])) {
                $memberDataWhere['nickname[~]'] = $memberData['nickname'];
            }
            if (isset($memberData['gender']) && is_numeric($memberData['gender'])) {
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
                array_push($conditionList, Util::getValueAsListByKey('member_id', $memberDataList));
            }
        }
        $memberRoleList = [];
        if ($role) {
            $memberRoleWhere = [];
            if (isset($role['name'])) {
                $roleModel = new RoleModel();
                $roleName = $role['name'];
                $roleListDB = $roleModel->select($roleModel->getColumns('id'), Medoo::raw("WHERE LOWER(`name`) LIKE LOWER('%$roleName%')"));
                $memberRoleWhere['role_id'] = $roleListDB ? Util::getValueAsListByKey('id', $roleListDB) : 0;
            }

            if ($memberRoleWhere) {
                $memberRoleModel->select(
                    $memberRoleModel->getColumns('member_id'),
                    $memberRoleWhere,
                    function ($_memberRole) use (&$memberRoleList) {
                        $memberRoleList[] = $_memberRole;
                    }
                );
                array_push($conditionList, Util::getValueAsListByKey('member_id', $memberRoleList));
            }
        }
        $memberList = [];
        if ($member) {
            $memberWhere = [];
            if (isset($member['username'])) {
                $memberWhere['username[~]'] = $member['username'];
            }
            if (isset($member['status']) && is_numeric($member['status'])) {
                $memberWhere['status'] = $member['status'];
            }

            if ($memberWhere) {
                $this->memberModel->select(
                    $this->memberModel->getColumns('id'),
                    $memberWhere,
                    function ($_member) use (&$memberList) {
                        $memberList[] = $_member;
                    }
                );
                array_push($conditionList, Util::getValueAsListByKey('id', $memberList));
            }
        }
        // \App\debug('memberDataList', $memberDataList);
        // \App\debug('memberRoleList', $memberRoleList);
        // \App\debug('memberList', $memberList);
        // \App\debug('conditionList', $conditionList);
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
        // Add role list
        for ($i = 0; $i < count($result['list']); $i++) {
            $roleList = $memberRoleModel->listRoleByMemberId($result['list'][$i]['member']['member_id']);
            $result['list'][$i]['roleList'] = $roleList;
        }

        return Result::success($result);
    }

    /**
     * Validate old password
     * 
     * @param string oldPassword
     * @return Result
     */
    public function validateOldPassword()
    {
        $oldPassword = strval($this->request->get('oldPassword'));
        if (empty($oldPassword)) {
            return Result::error('Old password does not exist');
        }

        $memberId = $this->authMember->member['id'];
        $member = $this->memberModel->getById(['password'], $memberId);

        if (!$this->memberModel->verifyPassword($oldPassword, $member['password'])) {
            return Result::error('Old password error');
        }
        return Result::success();
    }

    /**
     * Validate access token
     * 
     * @return Result
     */
    public function validateAccessToken()
    {
        // Read access token from header
        // if use POST or GET data, make sure the access token had been changed when refresh token
        $accessToken = $this->jwt->getTokenFromRequest();
        if (empty($accessToken)) {
            return Result::error('AccessToken does not exist', ResultCode::ACCESS_TOKEN_EXCEPTION);
        }
        $accessTokenValidation = $this->jwt->validateTokenRedis($accessToken);
        if (!$accessTokenValidation) {
            return Result::error('Invalid accessToken', ResultCode::ACCESS_TOKEN_EXCEPTION);
        }
        return Result::success();
    }

    /**
     * Refresh access token
     * 
     * @return Result
     */
    public function refreshAccessToken()
    {
        // Check old access token
        $oldAccessToken = $this->jwt->getTokenFromRequest();
        if (empty($oldAccessToken)) {
            return Result::error('Old accessToken does not exist', ResultCode::REFRESH_TOKEN_EXCEPTION);
        }
        $oldAuthMember = $this->jwt->getAuthMember($oldAccessToken);
        if (empty($oldAuthMember) || empty($oldAuthMember->member['id'])) {
            return Result::error('Old authMember does not exist, old accessToken error', ResultCode::REFRESH_TOKEN_EXCEPTION);
        }
        // Check refresh token
        $refreshToken = strval($this->request->get('refreshToken'));
        if (empty($refreshToken)) {
            return Result::error('RefreshToken does not exist', ResultCode::REFRESH_TOKEN_EXCEPTION);
        }
        if (!$this->jwt->validateToken($refreshToken)) {
            return Result::error('Invalid refreshToken', ResultCode::REFRESH_TOKEN_EXCEPTION);
        }
        $authMember = $this->jwt->getAuthMember($refreshToken);
        if (empty($authMember) || empty($authMember->member['id'])) {
            \App\log()->asError('Try refresh access token, but refreshToken error');
            return Result::error('New authMember does not exist, refreshToken error', ResultCode::REFRESH_TOKEN_EXCEPTION);
        }
        // is access token and refresh token from the same member
        if ($oldAuthMember->member['id'] !== $authMember->member['id']) {
            \App\log()->asError('Try refresh access token, but accessToken does not match the refreshToken');
            return Result::error('AccessToken does not match the refreshToken', ResultCode::REFRESH_TOKEN_EXCEPTION);
        }
        $result = $this->jwt->signAccessToken($authMember->member['id']);
        return Result::success($result);
    }

    /**
     * Update logined member password
     * 
     * @param string password
     * @return Result
     */
    public function updatePassword()
    {
        $newPassword = strval($this->request->get('newPassword'));
        $checkPassword = strval($this->request->get('checkPassword'));
        if (empty($newPassword)) {
            return Result::error('New password does not exist');
        }
        if (empty($checkPassword)) {
            return Result::error('New password2 does not exist');
        }
        if ($newPassword !== $checkPassword) {
            return Result::error('2 new passwords are not equal');
        }
        \App\log()->asInfo('Update password');
        $memberId = $this->authMember->member['id'];
        $this->memberModel->updateById(['password' => $newPassword, 'id' => $memberId]);
        return Result::success();
    }

    /**
     * Update logined member
     * 
     * @param object profile
     * @return Result
     */
    public function updateProfile()
    {
        $member = $this->request->get('member');
        $memberData = $this->request->get('memberData');
        if (empty($member) && empty($memberData)) {
            return Result::error('Member and memberData does not exist');
        }
        $memberId = $this->authMember->member['id'];
        if (!empty($member)) {
            $this->memberModel->updateById($member, $memberId);
        }
        if (!empty($memberData)) {
            $memberDataModel = new MemberDataModel();
            $memberDataModel->updateByMember_id($memberData, $memberId);
        }
        \App\log()->asInfo('Update profile');
        return Result::success();
    }


    /**
     * Update member (admin)
     * 
     * @param object member
     * @param object memberData
     * @return Result
     */
    public function updateDetail()
    {
        $member = $this->request->get('member');
        $memberData = $this->request->get('memberData');
        if (empty($member) || empty($memberData)) {
            return Result::error('Member or memberData does not exist');
        }
        if (!empty($member)) {
            \App\log()->asInfo(sprintf('Update member: [id:%d][username:%s]', $member['id'], $member['username']));
            $this->memberModel->updateById($member, $member['id']);
        }
        if (!empty($memberData)) {
            $memberDataModel = new MemberDataModel();
            $memberDataModel->updateByMember_id($memberData, $member['id']);
        }
        return Result::success();
    }

    /**
     * Add member
     * 
     * @param object member
     * @param object memberData
     * @param object role
     * @return Result
     */
    public function add()
    {
        $member = $this->request->get('member');
        $memberData = $this->request->get('memberData');
        $role = $this->request->get('role');
        if (empty($member)) {
            return Result::error('Member does not exist');
        }
        $memberId = $this->memberModel->add($member);
        \App\log()->asInfo(sprintf('Add member: [id:%d][username:%s]', $member['id'], $member['username']));
        if (!empty($memberData)) {
            $memberDataModel = new MemberDataModel();
            $memberDataModel->updateByMember_id($memberData, $memberId);
        }
        if (!empty($role)) {
            $memberRoleModel = new MemberRoleModel();
            $memberRoleModel->updateByRole_idMember_id([$role['id'], $memberId]);
        }
        return Result::success($memberId);
    }

    /**
     * Remove member by id
     * 
     * @param int id
     * @return Result
     */
    public function remove()
    {
        $memberId = intval($this->request->get('id'));
        if (empty($memberId)) {
            return Result::error('Member id does not exist');
        }
        $this->memberModel->deleteById($memberId);
        \App\log()->asInfo(sprintf('Remove member: [id:%d]', $memberId));

        $memberDataModel = new MemberDataModel();
        $memberDataModel->deleteByMember_id($memberId);

        $memberRoleModel = new MemberRoleModel();
        $memberRoleModel->deleteByMember_id($memberId);
        return Result::success();
    }
}
