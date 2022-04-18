<?php

namespace App\Model;

use App\Core\BaseModel;
use APP\Util\Ipv4Location;

/**
 * LogModel
 */
class LogModel extends BaseModel
{
    protected $table = 'log';

    protected $columns = [
        'id' => 'id [Int]',
        'member_id' => 'member_id [Int]',
        'member_username' => 'member_username',
        'level' => 'level [Int]',
        'content' => 'content',
        'ip' => 'ip [Int]',
        'ip_city' => 'ip_city',
        'extra' => 'extra [JSON]',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at'
    ];

    /**
     * Add log
     * 
     * @param $data
     * @return mixed id
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

        return $this->insert(array_merge($data, [
            'member_id' => \App\DI()->authMember->member->id,
            'member_username' => \App\DI()->authMember->member->username,
            'ip' => ip2long($ip),
            'ip_city' => implode('-', $ipCity),
        ]));
    }

    /**
     * Add info log
     * 
     * @param $content
     * @param $extra
     * @return mixed id
     */
    public function asInfo($content, $extra = null)
    {
        return $this->add([
            'level' => 0,
            'content' => $content,
            'extra [JSON]' => $extra,
        ]);
    }

    /**
     * Add warn log
     * 
     * @param $content
     * @param $extra
     * @return mixed id
     */
    public function asWarn($content, $extra = null)
    {
        return $this->add([
            'level' => 1,
            'content' => $content,
            'extra [JSON]' => $extra,
        ]);
    }

    /**
     * Add error log
     * 
     * @param $content
     * @param $extra
     * @return mixed id
     */
    public function asError($content, $extra = null)
    {
        return $this->add([
            'level' => 2,
            'content' => $content,
            'extra [JSON]' => $extra,
        ]);
    }
}
