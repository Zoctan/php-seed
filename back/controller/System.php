<?php

ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once dirname(__FILE__) . "/../function/Util.php";
require_once dirname(__FILE__) . "/../function/Result.php";
require_once dirname(__FILE__) . "/../model/System.php";

if (!empty($_POST) || !empty($_GET)) {
    switch ($_GET["action"]) {
            // 获取所有值
        case "getAll":
            getAll();
            break;
            // 获取值
        case "getValue":
            getValue();
            break;
            // 获取值列表
        case "getValues":
            getValues();
            break;
            // 添加
        case "create":
            create();
            break;
            // 更新
        case "update":
            update();
            break;
            // 更新值
        case "updateValue":
            updateValue();
            break;
            // 删除
        case "delete":
            delete();
            break;
        default:
            return;
    }
}

function getAll()
{
    $result = System::getInstance()->getAll();
    return Result::success($result);
}

function getValue()
{
    $key = Util::deleteSpace($_POST["key"]);
    $result = System::getInstance()->getValue($key);
    return Result::success($result);
}

function getValues()
{
    // a,b,c
    $keys = Util::deleteSpace($_POST["keys"]);
    $resultList = System::getInstance()->getValues($keys);
    return Result::success($resultList);
}

function create()
{
    $description = Util::deleteSpace($_POST["description"]);
    $key = Util::deleteSpace($_POST["key"]);
    $value = json_decode($_POST["value"]);
    $result = System::getInstance()->create($description, $key, $value);
    if (!$result) {
        Log::getInstance()->info($_COOKIE["id"], "添加配置失败，键：" . $key . "，值：" . json_encode($value));
        return Result::error("添加配置失败");
    }
    Log::getInstance()->info($_COOKIE["id"], "添加配置成功，键：" . $key . "，值：" . json_encode($value));
    return Result::success();
}

function update()
{
    $id = Util::deleteSpace($_POST["id"]);
    $description = Util::deleteSpace($_POST["description"]);
    $key = Util::deleteSpace($_POST["key"]);
    $value = json_decode($_POST["value"]);
    $result = System::getInstance()->update($id, $description, $key, $value);
    if (!$result) {
        Log::getInstance()->info($_COOKIE["id"], "修改配置失败，键：" . $key . "，值：" . json_encode($value));
        return Result::error("修改配置失败");
    }
    Log::getInstance()->info($_COOKIE["id"], "修改配置成功，键：" . $key . "，值：" . json_encode($value));
    return Result::success();
}

function updateValue()
{
    $key = Util::deleteSpace($_POST["key"]);
    $value = json_decode($_POST["value"]);
    $result = System::getInstance()->updateValue($key, $value);
    if (!$result) {
        Log::getInstance()->info($_COOKIE["id"], "修改配置失败，键：" . $key . "，值：" . json_encode($value));
        return Result::error("修改配置失败");
    }
    Log::getInstance()->info($_COOKIE["id"], "修改配置成功，键：" . $key . "，值：" . json_encode($value));
    return Result::success();
}

function delete()
{
    $id = Util::deleteSpace($_POST["id"]);
    $result = System::getInstance()->delete($id);
    if (!$result) {
        Log::getInstance()->info($_COOKIE["id"], "删除配置失败，配置(ID)：" . $id);
        return Result::error("删除配置失败");
    }
    Log::getInstance()->info($_COOKIE["id"], "删除配置成功，配置(ID)：" . $id);
    return Result::success();
}
