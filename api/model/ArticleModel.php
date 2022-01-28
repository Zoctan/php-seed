<?php

namespace App\Model;

use Medoo\Medoo;

use App\Core\BaseModel;

class ArticleModel extends BaseModel
{
    protected $table = "article";

    /**
     * 添加
     */
    public function add($article)
    {
        $articleId = $this->insert($article);
        if (!$articleId) {
            throw new \Exception("文章创建失败");
        }
        return $articleId;
    }
}
