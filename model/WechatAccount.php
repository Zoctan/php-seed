<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

require_once dirname(__FILE__) . '/../function/Util.php';
require_once dirname(__FILE__) . '/../function/MysqliDb.php';
require_once dirname(__FILE__) . '/System.php';

class WechatMember
{
    // 单例
    private static $instance;
    private $tableName = 'wechat_member';

    // 获取实例
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    /**
     * 分页查询
     */
    public function pageBy($currentPage = 0, $pageSize = 20, $keys = [], $values = [], $like = false)
    {
        $resultList = $this->getBy($keys, $values, $like);
        // 数据总条数
        $total = count($resultList);

        // 有分页
        if (!empty($currentPage) && $currentPage > 0) {
            $currentPage = (int) $currentPage;
            $pageSize = (int) $pageSize;
            $limitStart = ($currentPage - 1) * $pageSize;
            $resultList = $this->getBy($keys, $values, $like, $limitStart, $pageSize);
        }

        return Util::page($currentPage, $pageSize, $total, $resultList);
    }

    public function getBy($keys = [], $values = [], $like = false, $limitStart = -1, $limitSize = -1)
    {
        $len = count($values);
        $params = [];
        $sql = 'SELECT * FROM `$this->tableName` ';
        $sql .= $len > 0 ? ' WHERE ' : '';
        for ($i = 0; $i < $len; $i++) {
            if ($i != 0) {
                $sql .= ' AND ';
            }
            if (!$like) {
                $sql .= ' `$keys[$i]` = ?';
            } else {
                $sql .= ' UPPER(`$keys[$i]`) LIKE BINARY CONCAT('%',UPPER(?),'%')';
            }
            array_push($params, $values[$i]);
        }
        if ($limitStart != -1 && $limitSize != -1) {
            $sql .= ' LIMIT ?, ?';
            array_push($params, $limitStart, $limitSize);
        }
        return MysqliDb::getInstance()
            ->rawQuery($sql, count($params) > 0 ? $params : null);
    }

    public function getOneBy($keys = [], $values = [], $like = false)
    {
        $resultList = $this->getBy($keys, $values, $like);
        return count($resultList) == 0 ? null : $resultList[0];
    }

    /*
     * 绑定微信用户
     */
    public function bindMember($memberId, $openid, $nickname = '', $headimgurl = '')
    {
        if (empty($openid)) {
            return false;
        }
        $id = MysqliDb::getInstance()
            ->insert('`$this->tableName`', [
                'member_id' => $memberId,
                'openid' => $openid,
                // nickname 可能有特殊字符
                'nickname' => Util::deleteEmojiChar($nickname),
                'headimgurl' => $headimgurl,
            ]);
        return $id;
    }

    /*
     * 更新微信用户昵称和头像
     */
    public function updateByOpenid($openid, $nickname, $headimgurl)
    {
        $result = MysqliDb::getInstance()
            ->where('`openid`', $openid)
            ->update('`$this->tableName`', [
                'nickname' => Util::deleteEmojiChar($nickname),
                'headimgurl' => $headimgurl,
            ]);
        return $result;
    }

    /*
     * -------------------微信相关
     * https://developers.weixin.qq.com/doc/offimember/OA_Web_Apps/Wechat_webpage_authorization.html
     */

    /*
     * 获取用户信息
     */
    public function getWxUserInfo($accessToken, $openid)
    {
        $user = Util::httpGet('https://api.weixin.qq.com/sns/userinfo', [
            'access_token' => $accessToken,
            'openid' => $openid,
            'lang' => 'zh_CN',
        ]);
        if (!empty($user)) {
            $user = json_decode($user, true);
            return $user;
        }
        return null;
    }

    /*
     * 授权
     * scope：应用授权作用域
     *      snsapi_base：不弹出授权页面，直接跳转，只能获取用户openid；
     *      snsapi_userinfo：弹出授权页面，可通过openid拿到昵称、性别、所在地。并且即使在未关注的情况下，只要用户授权，也能获取其信息。
     */
    public function wxAuthorize($scope = 'snsapi_userinfo', $state = '')
    {
        $wechat = System::getInstance()->getValue('wechat');
        header('location: https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query([
            'appid' => $wechat['credential']['appId'],
            'redirect_uri' => $this->getUrl(),
            'response_type' => 'code',
            'scope' => $scope,
            // state：否，重定向后会带上state参数，开发者可以填写a-zA-Z0-9的参数值，最多128字节
            'state' => $state,
        ]) . '#wechat_redirect');
    }

    /*
     * 获取当前url
     */
    public function getUrl()
    {
        //获取协议类型
        $protocalPort = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        //获取当前执行脚本的url
        $phpSelf = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $pathInfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $queryString = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
        $relateUrl = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $phpSelf . (!empty($queryString) ? '?' . $queryString : $pathInfo);
        $url = $protocalPort . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relateUrl;
        return $url;
    }

    /*
     * 获取access_token
     */
    public function getWxAccessToken($code)
    {
        $wechat = System::getInstance()->getValue('wechat');
        $result = Util::httpGet('https://api.weixin.qq.com/sns/oauth2/access_token', [
            'appid' => $wechat['credential']['appId'],
            'secret' => $wechat['credential']['secret'],
            'code' => $code,
            'grant_type' => 'authorization_code',
        ]);
        if (!empty($result)) {
            $result = json_decode($result, true);
            return $result;
        }
        return null;
    }
}
