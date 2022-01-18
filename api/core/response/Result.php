<?php

namespace PHPSeed\Core\Response;

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

    public function toString()
    {
        $result = ["errno" => $this->errno];
        if (!empty($this->data)) {
            $result = array_merge($result, ["data" => $this->data]);
        }
        if (!empty($this->msg)) {
            $result = array_merge($result, ["msg" => $this->msg]);
        }
        return json_encode($result);
    }
}
