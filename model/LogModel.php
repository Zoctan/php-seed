<?php

namespace App\Model;

use App\Core\BaseModel;
use APP\Util\Ipv4Location;

class LogModel extends BaseModel
{
    protected $table = 'log';

    /*
     * 添加
     */
    public function add($data)
    {
        $ip = Ipv4Location::getIp();
        $ipInfo = Ipv4Location::getLocation($ip);

        $ipAddress = [$ipInfo['country'], $ipInfo['province'], $ipInfo['city']];
        $ipCity = [];
        for ($i = 0; $i < count($ipAddress); $i++) {
            if (!empty($ipAddress[$i])) {
                array_push($ipCity, $ipAddress[$i]);
            }
        }

        $data =  array_merge($data, [
            'member_id' => \App\DI()->authMember->id,
            'member_name' => \App\DI()->authMember->nickname,
            'ip' => ip2long($ip),
            'ip_city' => implode('-', $ipCity),
        ]);

        return $this->insert($data);
    }

    public function asInfo($content, $extra = null)
    {
        return $this->add([
            'level' => 0,
            'content' => $content,
            'extra' => $extra,
        ]);
    }


    public function asWarn($content, $extra = null)
    {
        return $this->add([
            'level' => 1,
            'content' => $content,
            'extra' => $extra,
        ]);
    }


    public function asError($content, $extra = null)
    {
        return $this->add([
            'level' => 2,
            'content' => $content,
            'extra' => $extra,
        ]);
    }
}