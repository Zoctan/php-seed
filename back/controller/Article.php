<?php

ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once dirname(__FILE__) . "/../function/Util.php";
require_once dirname(__FILE__) . "/../function/Result.php";
require_once dirname(__FILE__) . "/../model/Article.php";
require_once dirname(__FILE__) . "/../model/Log.php";

if (!empty($_POST) || !empty($_GET)) {
    switch ($_GET["action"]) {
            // 搜索文章
        case "search":
            searchArticle();
            break;
            // 文章详情
        case "detail":
            getArticleDetail();
            break;
            // 创建文章
        case "create":
            createArticle();
            break;
            // 修改文章
        case "update":
            updateArticle();
            break;
            // 删除文章封面
        case "deleteImage":
            deleteImage();
            break;
            // 修改文章状态
        case "changeStatus":
            changeStatus();
            break;
            // 修改文章展示状态
        case "changeShow":
            changeShow();
            break;
        default:
            return;
    }
}

function searchArticle()
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
    $pageResult = Article::getInstance()->pageBy($currentPage, $pageSize, $keys, $values, true);
    return Result::success($pageResult);
}

function getArticleDetail()
{
    $id = Util::deleteSpace($_POST["id"]);
    $result = Article::getInstance()->getOneBy(["id"], [$id]);
    if (empty($result)) {
        return Result::error("文章不存在");
    }
    return Result::success($result);
}

function createArticle()
{
    $data = [
        "order" => $_POST["order"],
        "admin_id" => $_COOKIE["id"],
        "images" => $_POST["images"],
        "title" => $_POST["title"],
        "content" => $_POST["content"],
        "brief" => $_POST["brief"],
        "channel_id" => $_POST["channelId"],
        "show" => $_POST["show"],
    ];
    $result = Article::getInstance()->create($data);
    if (!$result) {
        Log::getInstance()->info($_COOKIE["id"], "添加失败，文章：《" . $data["title"] . "》");
        return Result::error("创建文章失败");
    }
    Log::getInstance()->info($_COOKIE["id"], "添加成功，文章(ID)：" . $result . "《" . $data["title"] . "》");
    return Result::success($result);
}

function updateArticle()
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

function deleteImage()
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

function changeStatus()
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

function changeShow()
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
