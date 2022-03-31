<?php

namespace App\Core\Http;

use Symfony\Component\HttpFoundation\Response as BaseResponse;

class Response extends BaseResponse
{
    protected $config;
    protected $debug = [];
    protected $data = [];

    public function setDebug($key, $value)
    {
        $this->debug[$key] = $value;
        return $this;
    }

    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function send()
    {
        $this->config = \App\DI()->config;

        // 发送前再装载 debug 信息
        if ($this->config->app->debug && !empty($this->debug)) {
            $this->data[$this->config->app->response->structureMap->debug] = $this->debug;
        }

        switch ($this->config->app->response->type) {
            default:
            case "json":
                // JSON_UNESCAPED_UNICODE 中文也能显示
                $this->setContent(json_encode($this->data, JSON_UNESCAPED_UNICODE));
                break;
        }

        return parent::send();
    }
}
