<?php

namespace App\Core\Result;

use App\Core\Http\Response;

/**
 * Result
 */
class Result
{
    private $errno;
    private $msg;
    private $data;
    private $structureMap;

    private function __construct()
    {
        $this->structureMap = \App\DI()->config
            ? \App\DI()->config['controller']['response']['structureMap']
            : [
                'errno' => 'errno',
                'msg'   => 'msg',
                'data'  => 'data',
                'debug' => 'debug',
            ];
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
        (new Result())
            ->setErrno($resultCode[0])
            ->setData($data)
            ->setMsg(!empty($msg) ? $msg : $resultCode[1])
            ->send();
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
        (new Result())
            ->setErrno($resultCode[0])
            ->setMsg(!empty($msg) ? $msg : $resultCode[1])
            ->send();
    }

    private function setErrno($errno)
    {
        $this->errno = $errno;
        return $this;
    }

    private function setMsg($msg)
    {
        $this->msg = $msg;
        return $this;
    }

    private function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Using structure map to wrap result and get it
     */
    private function get()
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

    /**
     * Send response
     */
    private function send()
    {
        \App\DI()->get('response', new Response())
            ->setData($this->get())
            ->send();
    }
}
