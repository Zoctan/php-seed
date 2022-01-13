<?php

ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once dirname(__FILE__) . "/../function/Util.php";
require_once dirname(__FILE__) . "/../function/Result.php";
require_once dirname(__FILE__) . "/../model/Article.php";
require_once dirname(__FILE__) . "/../model/Log.php";

if (!empty($_POST) || !empty($_GET)) {
    switch ($_GET["action"]) {
            // 搜索日志
        case "search":
            searchLog();
            break;
            // 删除日志
        case "delete":
            deleteLog();
            break;
        default:
            return;
    }
}

function searchLog()
{
    $currentPage = Util::deleteSpace($_POST["currentPage"], 0);
    $pageSize = Util::deleteSpace($_POST["pageSize"], 20);
    $targetColumn = Util::deleteSpace($_POST["targetColumn"]);
    $searchValue = Util::deleteSpace($_POST["searchValue"]);

    $keys = [];
    $values = [];
    if (!empty($targetColumn) && !empty($searchValue)) {
        array_push($keys, $targetColumn);
        array_push($values, $searchValue);
    }
    $pageResult = Log::getInstance()->pageBy($currentPage, $pageSize, $keys, $values, true);
    return Result::success($pageResult);
}

function deleteLog()
{
    $id = $_POST["id"];
    $result = Log::getInstance()->delete($id);
    if (!$result) {
        Log::getInstance()->info($_COOKIE["id"], "删除失败，日志(ID)：" . $id);
        return Result::error("删除日志失败");
    }
    Log::getInstance()->info($_COOKIE["id"], "删除成功，日志(ID)：" . $id);
    return Result::success();
}
