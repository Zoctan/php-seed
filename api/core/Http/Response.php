<?php

namespace App\Core\Http;

use Symfony\Component\HttpFoundation\Response as BaseResponse;

class Response extends BaseResponse
{
    protected $debug = [];

    public function setDebug($key, $value)
    {
        $this->debug[$key] = $value;
        return $this;
    }

    public function send()
    {
        $this->ifDebug();
        return parent::send();
    }

    /**
     * 发送前再装载 debug 信息
     */
    public function ifDebug()
    {
        $config = \App\DI()->config;
        if ($config->app->debug && !empty($this->debug)) {
            // 解析成 json 数组
            $content = json_decode($this->getContent(), true);
            $content[$config->app->response->structureMap->debug] = $this->debug;
            $this->setContent(json_encode($content, JSON_UNESCAPED_UNICODE));
        }
    }
}
