<?php

namespace App\Controller;

use App\Model\ArticleModel;
use App\Model\MemberModel;
use App\Core\BaseController;
use App\Core\Response\ResultGenerator;

class ArticleController extends BaseController
{
    /**
     * @var ArticleModel
     */
    private $articleModel;

    public function __construct(ArticleModel $articleModel)
    {
        parent::__construct();
        $this->articleModel = $articleModel;
    }

    /**
     * 列表
     */
    public function list()
    {
        $currentPage = intval($this->request->get("currentPage", 0));
        $pageSize = intval($this->request->get("pageSize", 20));

        $memberName = strval($this->request->get("memberName"));
        $title = strval($this->request->get("title"));
        $content = strval($this->request->get("content"));
        $brief = strval($this->request->get("brief"));
        $channelId = intval($this->request->get("channelId"));
        $status = intval($this->request->get("status"));
        $show = intval($this->request->get("show"));
        $createdAt = strval($this->request->get("createdAt"));

        $where = [];
        if ($memberName) {
            $memberModel = new MemberModel();
            $member = $memberModel->getByUsername($memberName, ["id [Int]"]);
            $where["member_id"] = $member["id"];
        }
        if ($title) {
            $where["title[~]"] = $title;
        }
        if ($content) {
            $where["content[~]"] = $content;
        }
        if ($brief) {
            $where["brief[~]"] = $brief;
        }
        if ($channelId) {
            $where["channel_id"] = $channelId;
        }
        if ($status) {
            $where["status"] = $status;
        }
        if ($show) {
            $where["show"] = $show;
        }
        if ($createdAt) {
            $where["created_at"] = $createdAt;
        }

        $result =  $this->articleModel->page($currentPage, $pageSize, [
            "id [Int]",
            "member_id [Int]",
            "order [Int]",
            "images",
            "title",
            "brief",
            "channel_id [Int]",
            "status [Int]",
            "show [Int]",
            "created_at",
            "updated_at",
        ], $where);
        return ResultGenerator::successWithData($result);
    }

    /**
     * 详情
     */
    public function detail()
    {
        $id = intval($this->request->get("id"));
        $result = $this->articleModel->getById([
            "id [Int]",
            "member_id [Int]",
            "order [Int]",
            "images",
            "title",
            "content",
            "brief",
            "channel_id [Int]",
            "status [Int]",
            "show [Int]",
            "extra [JSON]",
            "created_at",
            "updated_at",
        ], $id);
        return ResultGenerator::successWithData($result);
    }

    /**
     * 添加
     */
    public function add()
    {
        $article = [
            "member_id" => intval($this->request->get("memberId")),
            "order" => intval($this->request->get("order")),
            "images" => strval($this->request->get("images")),
            "title" => strval($this->request->get("title")),
            "content" => strval($this->request->get("content")),
            "brief" => strval($this->request->get("brief")),
            "channel_id" => intval($this->request->get("channelId")),
            "show" => intval($this->request->get("show")),
        ];

        $result = $this->articleModel->add($article);
        if (!$result) {
            return ResultGenerator::errorWithMsg("创建文章失败");
        }
        return ResultGenerator::successWithData($result);
    }

    // 修改文章
    public function updateArticle()
    {
        $data = [
            "id" => $_POST["id"],
            "order" => $_POST["order"],
            "images" => $_POST["images"],
            "title" => $_POST["title"],
            "content" => $_POST["content"],
            "brief" => $_POST["brief"],
            "channel_id" => $_POST["channelId"],
            "show" => $_POST["show"],
        ];
        $result = Article::getInstance()->update($data);
        if (!$result) {
            Log::getInstance()->info($_COOKIE["id"], "修改失败，文章(ID)：" . $data["id"] . "《" . $data["title"] . "》");
            return Result::error("修改文章失败");
        }
        Log::getInstance()->info($_COOKIE["id"], "修改成功，文章(ID)：" . $data["id"] . "《" . $data["title"] . "》");
        return Result::success();
    }

    // 删除文章封面
    public function deleteImage()
    {
        $data = [
            "id" => $_POST["id"],
            "images" => $_POST["images"],
        ];
        $result = Article::getInstance()->update($data);
        if (!$result) {
            return Result::error("删除封面失败");
        }
        return Result::success();
    }

    public function changeStatus()
    {
        $data = [
            "id" => $_POST["id"],
            "status" => $_POST["status"],
        ];
        $result = Article::getInstance()->update($data);
        if (!$result) {
            Log::getInstance()->info($_COOKIE["id"], "修改审核状态失败，文章(ID)：" . $data["id"]);
            return Result::error("修改审核状态失败");
        }
        Log::getInstance()->info($_COOKIE["id"], "修改审核状态成功，文章(ID)：" . $data["id"]);
        return Result::success();
    }

    // 修改文章展示状态
    public function changeShow()
    {
        $data = [
            "id" => $_POST["id"],
            "show" => $_POST["show"],
        ];
        $result = Article::getInstance()->update($data);
        if (!$result) {
            Log::getInstance()->info($_COOKIE["id"], "修改展示状态失败，文章(ID)：" . $data["id"]);
            return Result::error("修改展示状态失败");
        }
        Log::getInstance()->info($_COOKIE["id"], "修改展示状态成功，文章(ID)：" . $data["id"]);
        return Result::success();
    }
}
