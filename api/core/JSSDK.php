<?php

namespace PHPSeed\Core;

use Exception;
use Predis\Client;
use PHPSeed\Core\DI;

/**
 * 微信JSSDK
 * https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/JS-SDK.html
 */
class JSSDK
{
    /**
     * @var object
     */
    private $config;

    /**
     * @var Client
     */
    private $cache;

    private $appId;
    private $appSecret;

    private $jssdkTicketKey = "jssdk_ticket";
    private $accessTokenKey = "jssdk_access_token";

    public function __construct()
    {
        $this->config = DI::getInstance()->config->wechat;
        $this->cache = DI::getInstance()->cache;

        $this->appId = $this->config->credential->appId;
        $this->appSecret = $this->config->credential->appSecret;
    }

    public function getSignPackage()
    {
        $jsapiTicket = $this->getJsApiTicket();
        if (empty($jsapiTicket)) {
            return null;
        }

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" || $_SERVER["SERVER_PORT"] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = Util::randomStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $rawString = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($rawString);
        return [
            "appId" => $this->appId,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $rawString,
        ];
    }

    private function getJsApiTicket()
    {
        // 先从缓存里找，不然再请求微信服务器
        $jsapiTicket = $this->cache->get($this->jssdkTicketKey);
        if ($jsapiTicket) {
            return $jsapiTicket;
        }

        $accessToken = $this->getAccessToken();

        // https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/JS-SDK.html#62
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
        $res = json_decode(Util::httpGet($url));
        if (empty($res)) {
            throw new Exception("内部服务器请求微信服务器 jsapi_ticket 出现错误，请重试");
        }
        if (isset($res->errcode) && $res->errcode == 40001) {
            // access_token 过期了，更新一下
            $accessToken = $this->updateAccessToken();
            return $this->getJsApiTicket();
        }

        // 微信的token设置了过期时间7200
        $this->cache->setex($this->jssdkTicketKey, 7200, $res->ticket);
        return $res->ticket;
    }

    private function getAccessToken()
    {
        $access_token = $this->cache->get($this->accessTokenKey);
        if (empty($access_token)) {
            // 过期了，刷新
            return $this->updateAccessToken();
        }
        return $access_token;
    }

    private function updateAccessToken()
    {
        // https://developers.weixin.qq.com/doc/offiaccount/Basic_Information/Get_access_token.html
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
        $res = json_decode(Util::httpGet($url));
        if (empty($res) || (isset($res->errcode) && $res->errcode != 0)) {
            throw new Exception("内部服务器请求微信服务器 access_token 出现错误，请重试");
        }

        $this->cache->setex($this->accessTokenKey, $res->expires_in, $res->access_token);
        return $res->access_token;
    }
}
