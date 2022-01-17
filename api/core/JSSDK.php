<?php
namespace Seed\Core;

require_once dirname(__FILE__) . "/Util.php";
require_once dirname(__FILE__) . "/../model/System.php";

class JSSDK
{
    private $appId;
    private $appSecret;

    public function __construct()
    {
        $wechat = System::getInstance()->getValue("wechat");
        $this->appId = $wechat["credential"]["appId"];
        $this->appSecret = $wechat["credential"]["appSecret"];
    }

    public function getSignPackage()
    {
        $jsapiTicket = $this->getJsApiTicket();

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" || $_SERVER["SERVER_PORT"] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = Util::randomStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = "";
        if (empty($jsapiTicket)) {
            return [
                "appId" => $this->appId,
                "nonceStr" => $nonceStr,
                "timestamp" => $timestamp,
                "url" => $url,
                "signature" => $signature,
                "rawString" => $string,
            ];
        } else {
            $signature = sha1($string);
        }
        return [
            "appId" => $this->appId,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string,
        ];
    }

    private function getJsApiTicket()
    {
        $key = "jsapi_ticket";
        if (!isset($_COOKIE[$key])) {
            $accessToken = $this->getAccessToken();
            if (!empty($accessToken)) {
                $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
                $res = json_decode(Util::httpGet($url));
                // Util::consoleDebug($res);
                if (!empty($res)) {
                    if (isset($res->errcode)) {
                        if ($res->errcode == 40001) {
                            // access_token 过期了，更新一下
                            $this->updateAccessToken();
                            // 重新获取 ticket
                            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
                            $res = json_decode(Util::httpGet($url));
                        }
                    }
                    if (isset($res->ticket)) {
                        // 不用 SESSION，因为无法控制过期时间，而腾讯的token设置了过期时间7200
                        setcookie($key, $res->ticket, time() + 7200);
                        return $res->ticket;
                    }
                }
            }
            // 获取有问题，5分钟后再获取
            setcookie($key, "x", time() + 300);
            return null;
        } else {
            return $_COOKIE[$key];
        }
    }

    private function getAccessToken()
    {
        // 数据库中的access_token
        $value = System::getInstance()->getValue("jssdk");
        $access_token = $value["access_token"];
        if ($this->isExpire($value["expires_in"], $value["get_time"])) {
            // 过期了，刷新
            return $this->updateAccessToken();
        }
        return $access_token;
    }

    private function updateAccessToken()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
        $res = json_decode(Util::httpGet($url));
        // Util::consoleDebug($res);
        if (!empty($res)) {
            if (isset($res->errcode) && $res->errcode != 0) {
                return null;
            }
            if (isset($res->access_token)) {
                $access_token = $res->access_token;
                date_default_timezone_set("prc");
                System::getInstance()
                    ->updateValue("jssdk", [
                        "access_token" => $access_token,
                        "expires_in" => $res->expires_in,
                        "get_time" => date("Y-m-d H:i:s", time()),
                    ]);
                return $access_token;
            }
        }
        return null;
    }

    private function isExpire($expires_in, $get_time)
    {
        date_default_timezone_set("prc");
        $expireTime = date("Y-m-d H:i:s", strtotime($get_time . " + " . $expires_in . " second"));
        $nowTime = date("Y-m-d H:i:s", time());
        return strtotime($expireTime) < strtotime($nowTime);
    }
}
