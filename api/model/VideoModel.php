<?php

namespace App\Model;

use Medoo\Medoo;

use App\Core\BaseModel;

class VideoModel extends BaseModel
{
    protected $table = "article";

    /**
     * 添加
     */
    public function add($video)
    {
        $videoId = $this->insert($video);
        if (!$videoId) {
            throw new \Exception("视频创建失败");
        }
        return $videoId;
    }
}
