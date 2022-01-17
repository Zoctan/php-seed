<?php

ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once dirname(__FILE__) . "/../function/Util.php";
require_once dirname(__FILE__) . "/../function/Result.php";
require_once dirname(__FILE__) . "/../model/Video.php";

if (!empty($_POST) || !empty($_GET)) {
    switch ($_GET["action"]) {
            // 搜索视频
        case "search":
            searchVideo();
            break;
            // 视频详情
        case "detail":
            getVideoDetail();
            break;
            // 取回视频资源
        case "fetch":
            fetchVideo();
            break;
            // 创建视频
        case "create":
            createVideo();
            break;
            // 修改视频
        case "update":
            updateVideo();
            break;
            // 删除视频封面
        case "deleteImage":
            deleteImage();
            break;
            // 删除视频
        case "deleteVideo":
            deleteVideo();
            break;
            // 修改视频状态
        case "changeStatus":
            changeStatus();
            break;
            // 修改视频展示状态
        case "changeShow":
            changeShow();
            break;
        default:
            return;
    }
}

function searchVideo()
{
    $currentPage = Util::deleteSpace($_POST["currentPage"], 0);
    $pageSize = Util::deleteSpace($_POST["pageSize"], 20);
    $targetColumn = Util::deleteSpace($_POST["targetColumn"]);
    $searchValue = Util::deleteSpace($_POST["searchValue"]);

    $keys = [];
    $values = [];
    if (isset($_POST["status"])) {
        $status = Util::deleteSpace($_POST["status"]);
        array_push($keys, "status");
        array_push($values, $status);
    }
    if (isset($_POST["show"])) {
        $show = Util::deleteSpace($_POST["show"]);
        array_push($keys, "show");
        array_push($values, $show);
    }

    if (!empty($targetColumn) && !empty($searchValue)) {
        array_push($keys, $targetColumn);
        array_push($values, $searchValue);
    }
    $pageResult = Video::getInstance()->pageBy($currentPage, $pageSize, $keys, $values, true);
    return Result::success($pageResult);
}

function getVideoDetail()
{
    $id = Util::deleteSpace($_POST["id"]);
    $result = Video::getInstance()->getOneBy(["id"], [$id]);
    if (empty($result)) {
        return Result::error("视频不存在");
    }
    return Result::success($result);
}

function fetchVideo()
{
    $id = $_GET["id"];
    $video = Video::getInstance()->getOneBy(["id"], [$id]);
    return Result::success($video["videos"][0]);
}

function createVideo()
{
    $data = [
        "admin_id" => $_COOKIE["id"],
        "images" => $_POST["images"],
        "title" => $_POST["title"],
        "videos" => $_POST["videos"],
        "brief" => $_POST["brief"],
        "channel_id" => $_POST["channelId"],
        "show" => $_POST["show"],
    ];
    $result = Video::getInstance()->create($data);
    if (!$result) {
        Log::getInstance()->info($_COOKIE["id"], "添加失败，视频：《" . $data["title"] . "》");
        return Result::error("添加视频失败");
    }
    Log::getInstance()->info($_COOKIE["id"], "添加成功，视频(ID)：" . $result . "《" . $data["title"] . "》");
    return Result::success();
}

function updateVideo()
{
    $data = [
        "id" => $_POST["id"],
        "images" => $_POST["images"],
        "title" => $_POST["title"],
        "videos" => $_POST["videos"],
        "brief" => $_POST["brief"],
        "channel_id" => $_POST["channelId"],
        "show" => $_POST["show"],
    ];
    $result = Video::getInstance()->update($data);
    if (!$result) {
        Log::getInstance()->info($_COOKIE["id"], "修改失败，视频(ID)：" . $data["id"] . "《" . $data["title"] . "》");
        return Result::error("修改视频失败");
    }
    Log::getInstance()->info($_COOKIE["id"], "修改成功，视频(ID)：" . $data["id"] . "《" . $data["title"] . "》");
    return Result::success();
}

function deleteImage()
{
    $data = [
        "id" => $_POST["id"],
        "images" => $_POST["images"],
    ];
    $result = Video::getInstance()->update($data);
    if (!$result) {
        return Result::error("删除封面失败");
    }
    return Result::success();
}

function deleteVideo()
{
    $data = [
        "id" => $_POST["id"],
        "videos" => $_POST["videos"],
    ];
    $result = Video::getInstance()->update($data);
    if (!$result) {
        Log::getInstance()->info($_COOKIE["id"], "删除失败，视频(ID)：" . $data["id"]);
        return Result::error("删除视频失败");
    }
    Log::getInstance()->info($_COOKIE["id"], "删除成功，视频(ID)：" . $data["id"]);
    return Result::success();
}

function changeStatus()
{
    $data = [
        "id" => $_POST["id"],
        "status" => $_POST["status"],
    ];
    $result = Video::getInstance()->update($data);
    if (!$result) {
        Log::getInstance()->info($_COOKIE["id"], "修改审核状态失败，视频(ID)：" . $data["id"]);
        return Result::error("修改审核状态失败");
    }
    Log::getInstance()->info($_COOKIE["id"], "修改审核状态成功，视频(ID)：" . $data["id"]);
    return Result::success();
}

function changeShow()
{
    $data = [
        "id" => $_POST["id"],
        "show" => $_POST["show"],
    ];
    $result = Video::getInstance()->update($data);
    if (!$result) {
        Log::getInstance()->info($_COOKIE["id"], "修改展示状态失败，视频(ID)：" . $data["id"]);
        return Result::error("修改展示状态失败");
    }
    Log::getInstance()->info($_COOKIE["id"], "修改展示状态成功，视频(ID)：" . $data["id"]);
    return Result::success();
}
