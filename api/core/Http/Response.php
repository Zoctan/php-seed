<?php

namespace PHPSeed\Core\Http;

use PHPSeed\Core\DI;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class Response extends BaseResponse
{
    protected $debug = [];

    public function setDebug($key, $value)
    {
        $this->debug = array_merge($this->debug, [$key => $value]);
        return $this;
    }

    public function send()
    {
        $this->setCorsHeader();
        $this->ifDebug();
        return parent::send();
    }

    /**
     * 发送前再装载 debug 信息
     */
    public function ifDebug()
    {
        $isDebug = DI::getInstance()->config->app->debug;
        if ($isDebug && !empty($this->debug)) {
            // 解析成 json 数组
            $content = json_decode($this->getContent(), true);
            $content = array_merge($content, [
                "debug" => $this->debug,
            ]);
            $this->setContent(json_encode($content));
        }
    }

    /**
     * 设置跨域头
     */
    public function setCorsHeader()
    {
        // 允许任何网址请求
        $this->headers->set("Access-Control-Allow-Origin", "*");
        // 返回的类型
        $this->headers->set("Content-type", "application/json;charset=UTF-8");
        // 允许请求的类型
        $this->headers->set("Access-Control-Allow-Methods", "GET,POST,DELETE,PUT,PATCH,OPTIONS");
        // 不允许带 cookies
        // 请求框架是 axios，关闭 withCredentials: false
        $this->headers->set("Access-Control-Allow-Credentials", "false");
        // 设置允许自定义请求头的字段
        $this->headers->set("Access-Control-Allow-Headers", "Content-Type,Content-Length,Authorization");
    }
}
