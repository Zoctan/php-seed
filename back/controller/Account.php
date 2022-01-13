<?php

ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once dirname(__FILE__) . "/../function/Util.php";
require_once dirname(__FILE__) . "/../function/Result.php";
require_once dirname(__FILE__) . "/../model/Account.php";
require_once dirname(__FILE__) . "/../model/Log.php";

if (!empty($_POST) || !empty($_GET)) {
    switch ($_GET["action"]) {
            // 刷新用户登陆状态
        case "refresh":
            refresh();
            break;
            // 创建用户
        case "create":
            createAccount();
            break;
            // 单个用户
        case "get":
            getAccount();
            break;
            // 列表用户
        case "list":
            listAccount();
            break;
            // 搜索用户
        case "search":
            searchAccount();
            break;
            // 用户登出
        case "logout":
            logout();
            break;
            // 修改
        case "update":
            update();
            break;
            // 修改角色
        case "updateRole":
            updateRole();
            break;
        default:
            return;
    }
}

function refresh()
{
    $account = Account::getInstance()->getOneBy(["openid"], [$_COOKIE["openid"]]);
    // 测试用户可能被清空了数据，要重新登录
    if (empty($account)) {
        Util::deleteSessionAndCookie();
        return Result::error("凭证失效，请重新刷新登录！");
    }
    Account::getInstance()->saveLoginStatus($account);
    return Result::success($account);
}

function createAccount()
{
    $info = $_POST["info"];
    $result = Account::getInstance()->create($info);
    if (!$result) {
        return Result::error("创建用户失败");
    }
    Log::getInstance()->info($_COOKIE["id"], "创建用户成功，用户(ID)：" . $result);
    return Result::success();
}

function getAccount()
{
    $id = $_POST["id"];
    $data = Account::getInstance()->getOneBy(["id"], [$id]);
    return Result::success($data);
}

function listAccount()
{
    $currentPage = Util::deleteSpace($_POST["currentPage"], 0);
    $pageSize = Util::deleteSpace($_POST["pageSize"], 20);
    $pageResult = Account::getInstance()->pageBy($currentPage, $pageSize);
    return Result::success($pageResult);
}

function searchAccount()
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
    $pageResult = Account::getInstance()->pageBy($currentPage, $pageSize, $keys, $values, true);
    return Result::success($pageResult);
}

function logout()
{
    Util::deleteSessionAndCookie();
    return Result::success();
}

function update()
{
    $info = $_POST["info"];
    $info["id"] = $_COOKIE["id"];
    $result = Account::getInstance()->update($info);
    if (!$result) {
        Log::getInstance()->info($_COOKIE["id"], "修改信息失败，用户(ID)：" . $info["id"]);
        return Result::error("修改信息失败");
    }
    Log::getInstance()->info($_COOKIE["id"], "修改信息成功，用户(ID)：" . $info["id"]);
    return Result::success();
}

function updateRole()
{
    $id = Util::deleteSpace($_POST["id"], 0);
    $role = Util::deleteSpace($_POST["role"]);

    $result = Account::getInstance()->updateRole($id, $role);
    if (!$result) {
        Log::getInstance()->info($_COOKIE["id"], "修改角色失败，用户(ID)：" . $id);
        return Result::error("修改角色失败");
    }
    Log::getInstance()->info($_COOKIE["id"], "修改角色成功，用户(ID)：" . $id);
    return Result::success();
}
