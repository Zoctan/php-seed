<?php

namespace App\Core\Response;

use App\Core\Http\Response;

/**
 * 响应结果
 */
class Result
{
    // 状态码
    private $errno;
    // 消息
    private $msg;
    // 数据
    private $data;
    // 响应结构字段映射
    private $structureMap;

    public function __construct()
    {
        $this->structureMap = \App\DI()->config['app']['response']['structureMap'];
    }

    public function setErrno($errno)
    {
        $this->errno = $errno;
        return $this;
    }

    public function setMsg($msg)
    {
        $this->msg = $msg;
        return $this;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function get()
    {
        $result = [];
        $result[$this->structureMap['errno']] = $this->errno;
        if (!empty($this->msg)) {
            $result[$this->structureMap['msg']] = $this->msg;
        }
        if (!empty($this->data)) {
            $result[$this->structureMap['data']] = $this->data;
        }
        return $result;
    }

    public function response()
    {
        $response = \App\DI()->get('response', new Response());
        $response->setData($this->get());
        $response->send();
    }
}
