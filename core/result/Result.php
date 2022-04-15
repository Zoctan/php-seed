<?php

namespace App\Core\Result;

/**
 * Result
 */
class Result
{
    private $errno;
    private $msg;
    private $data;
    private $structureMap;

    public function __construct()
    {
        $this->structureMap = \App\DI()->config['controller']['response']['structureMap'];
    }

    /**
     * Get success result
     * 
     * @param mixed $data
     * @param string $msg
     * @param array $resultCode
     */
    public static function success($data = null, string $msg = '', array $resultCode = ResultCode::SUCCEED)
    {
        if ($resultCode[0] !== 0) {
            throw new \Exception('Do not use error result code by call success function');
        }
        return (new Result())
            ->setErrno($resultCode[0])
            ->setData($data)
            ->setMsg(!empty($msg) ? $msg : $resultCode[1]);
    }

    /**
     * Get error result
     * 
     * @param string $msg
     * @param array $resultCode
     */
    public static function error(string $msg = '', array $resultCode = ResultCode::FAILED)
    {
        if ($resultCode[0] === 0) {
            throw new \Exception('Do not use success result code by call error function');
        }
        return (new Result())
            ->setErrno($resultCode[0])
            ->setMsg(!empty($msg) ? $msg : $resultCode[1]);
    }

    private function setErrno($errno)
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

    /**
     * Using structure map to wrap result and get it
     */
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
}
