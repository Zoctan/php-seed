<?php

namespace PHPSeed\Core\Response;

use PHPSeed\Core\DI;

/**
 * 响应结果
 */
class Result
{
    // 状态码
    private $errno = 0;
    // 消息
    private $msg = "";
    // 数据
    private $data = null;

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

    public function response()
    {
        $response = DI::getInstance()->response;
        $result = ["errno" => $this->errno];
        if (!empty($this->data)) {
            $result["data"] = $this->data;
        }
        if (!empty($this->msg)) {
            $result["msg"] = $this->msg;
        }
        // JSON_UNESCAPED_UNICODE 中文也能显示
        $response->setContent(json_encode($result, JSON_UNESCAPED_UNICODE));
        $response->send();
    }
}
