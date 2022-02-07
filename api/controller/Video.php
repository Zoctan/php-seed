<?php

namespace App\Controller;

use App\Model\VideoModel;
use App\Model\MemberModel;
use App\Core\BaseController;
use App\Core\Response\ResultGenerator;

class VideoController extends BaseController
{
    /**
     * @var VideoModel
     */
    private $videoModel;

    public function __construct(VideoModel $videoModel)
    {
        parent::__construct();
        $this->videoModel = $videoModel;
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

        $result =  $this->videoModel->page($currentPage, $pageSize, [
            "id [Int]",
            "member_id [Int]",
            "order [Int]",
            "videos",
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
        $result = $this->videoModel->getById([
            "id [Int]",
            "member_id [Int]",
            "order [Int]",
            "videos",
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
        $data = [
            "member_id" => intval($this->request->get("memberId")),
            "order" => intval($this->request->get("order")),
            "videos" => strval($this->request->get("videos")),
            "title" => strval($this->request->get("title")),
            "content" => strval($this->request->get("content")),
            "brief" => strval($this->request->get("brief")),
            "channel_id" => intval($this->request->get("channelId")),
            "show" => intval($this->request->get("show")),
        ];

        $result = $this->videoModel->add($data);
        if (!$result) {
            return ResultGenerator::errorWithMsg("创建文章失败");
        }
        return ResultGenerator::successWithData($result);
    }

    // 修改文章
    public function updateArticle()
    {
        $data = [
            "id" => intval($this->request->get("id")),
            "order" => intval($this->request->get("order")),
            "videos" => strval($this->request->get("videos")),
            "title" => strval($this->request->get("title")),
            "content" => strval($this->request->get("content")),
            "brief" => strval($this->request->get("brief")),
            "channel_id" => intval($this->request->get("channelId")),
            "show" => intval($this->request->get("show")),
        ];

        $result = $this->videoModel->update($data);
        if (!$result) {
            return ResultGenerator::errorWithMsg("修改文章失败");
        }
        return ResultGenerator::success();
    }

    // 删除文章封面
    public function deleteImage()
    {
        $data = [
            "id" => intval($this->request->get("id")),
            "videos" => strval($this->request->get("videos")),
        ];

        $result = $this->videoModel->update($data);
        if (!$result) {
            return ResultGenerator::errorWithMsg("删除封面失败");
        }
        return ResultGenerator::success();
    }

    public function changeStatus()
    {
        $data = [
            "id" => intval($this->request->get("id")),
            "status" => intval($this->request->get("status")),
        ];

        $result = $this->videoModel->update($data);
        if (!$result) {
            return ResultGenerator::errorWithMsg("修改审核状态失败");
        }
        return ResultGenerator::success();
    }

    // 修改文章展示状态
    public function changeShow()
    {
        $data = [
            "id" => intval($this->request->get("id")),
            "show" => intval($this->request->get("show")),
        ];

        $result = $this->videoModel->update($data);
        if (!$result) {
            return ResultGenerator::errorWithMsg("修改展示状态失败");
        }
        return ResultGenerator::success();
    }
}
