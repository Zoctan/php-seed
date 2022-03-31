<?php

namespace App\Util;

use Predis\Client;
use App\Core\Singleton;

/**
 * 微信JSSDK
 * https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/JS-SDK.html
 */
class JssdkUtil
{
    use Singleton;

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

    private $jssdkTicketKey = 'jssdk_ticket';
    private $accessTokenKey = 'jssdk_access_token';

    private function __construct()
    {
        $this->config = \App\DI()->config['wechat'];
        $this->cache = \App\DI()->cache;

        $this->appId = $this->config['credential']['appId'];
        $this->appSecret = $this->config['credential']['appSecret'];
    }

    public function getSignPackage()
    {
        $jsapiTicket = $this->getJsApiTicket();
        if (empty($jsapiTicket)) {
            return null;
        }

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
        $url = '$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]';

        $timestamp = time();
        $nonceStr = Util::randomStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $rawString = 'jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url';
        $signature = sha1($rawString);
        return [
            'appId' => $this->appId,
            'nonceStr' => $nonceStr,
            'timestamp' => $timestamp,
            'url' => $url,
            'signature' => $signature,
            'rawString' => $rawString,
        ];
    }

    /**
     * 获取 jsapi_ticket
     * https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/JS-SDK.html#62
     */
    private function getJsApiTicket($tryTime = 0)
    {
        // 先从缓存里找，不然再请求微信服务器
        $jsapiTicket = $this->cache->get($this->jssdkTicketKey);
        if ($jsapiTicket) {
            return $jsapiTicket;
        }

        $accessToken = $this->getAccessToken();

        $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken';

        // 使用证书访问：https://docs.guzzlephp.org/en/stable/request-options.html#verify
        $response = \App\DI()->curl->request('GET', $url, ['verify' => $this->config['sslCert']]);
        $response = json_decode($response->getBody());
        if (empty($response) || (isset($response->errcode) && $response->errcode != 0)) {
            if ($tryTime == 3) {
                throw new \Exception('内部服务器请求微信服务器 jsapi_ticket 出现错误，请联系管理员：' . json_encode($response));
            }

            $tryTime++;
            return $this->getJsApiTicket($tryTime);
        }

        // 微信的token设置了过期时间7200
        $this->cache->setex($this->jssdkTicketKey, 7200, $response->ticket);
        return $response->ticket;
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

    /**
     * 更新 access_token
     * https://developers.weixin.qq.com/doc/offiaccount/Basic_Information/Get_access_token.html
     */
    private function updateAccessToken($tryTime = 0)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret';

        $response = \App\DI()->curl->request('GET', $url, ['verify' => $this->config['sslCert']]);
        $response = json_decode($response->getBody());
        if (empty($response) || (isset($response->errcode) && $response->errcode != 0)) {
            if ($tryTime == 3) {
                throw new \Exception('内部服务器请求微信服务器 access_token 出现错误，请联系管理员：' . json_encode($response));
            }

            $tryTime++;
            return $this->updateAccessToken($tryTime);
        }

        $this->cache->setex($this->accessTokenKey, $response->expires_in, $response->access_token);
        return $response->access_token;
    }
}
