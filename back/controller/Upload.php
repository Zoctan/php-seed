<?php

ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once dirname(__FILE__) . "/../function/Util.php";
require_once dirname(__FILE__) . "/../function/Result.php";
require_once dirname(__FILE__) . "/../model/Account.php";

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST,GET,OPTIONS');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

// 文件保存目录
$uploadDir = "upload/";
// 当前后端文件夹目录
$currentBackDir = "back/";

if (!empty($_POST) || !empty($_GET)) {
    switch ($_GET["action"]) {
            // 创建资源
        case "create":
            create($uploadDir, $currentBackDir);
            break;
            // 删除资源
        case "delete":
            delete($currentBackDir);
            break;
            // 删除视频资源
        case "deleteVideo":
            deleteVideo($currentBackDir);
            break;
        default:
            return;
    }
}

function create($uploadDir, $currentBackDir)
{
    // 实际保存目录
    $realUploadDir = implode("/", [$_SERVER["DOCUMENT_ROOT"], $currentBackDir, $uploadDir]);

    // 上传到子文件夹
    if (!empty($_GET["dir"])) {
        $realUploadDir .= $_GET["dir"] . "/";
        $uploadDir .= $_GET["dir"] . "/";
        // 不存在则创建
        if (!is_dir($realUploadDir)) {
            mkdir($realUploadDir, 0777, true);
        }
    }

    // 成功上传的文件列表
    $successList = [];
    // 失败上传的文件列表
    $failList = [];
    foreach ($_FILES as $item) {
        // 文件名称
        $fileName = $item["name"];
        // 文件类型
        $fileType = $item["type"];
        // 文件大小
        $fileSize = $item["size"];
        // 文件的临时保存路径
        $fileTmp = $item["tmp_name"];

        $isUploadSuccess = is_uploaded_file($fileTmp);
        if (!$isUploadSuccess) {
            // 失败
            array_push($failList, $fileName);
            continue;
        }

        // 生成目标文件的文件名
        // test.jpg -> asdfghjkl.jpg
        $fileNameArray = explode(".", $fileName);
        do {
            // 设置随机文件名长度
            $fileName = Util::randomStr(15);
            $randomFileName = implode(".", [$fileName, end($fileNameArray)]);
            $realUploadFile = $realUploadDir . $randomFileName;
            // 返回的应该是网站目录下的上传目录，而不是D:\xx这样的目录地址
            $uploadFile = $uploadDir . $randomFileName;
        } while (file_exists($realUploadFile));

        $isMoveSuccess = move_uploaded_file($fileTmp, $realUploadFile);
        if ($isMoveSuccess) {
            array_push($successList, [
                "name" => $randomFileName,
                "url" => $uploadFile,
            ]);
        }
    }

    // var_dump($_FILES);

    // var_dump($successList);

    if (count($successList) == 0) {
        return Result::error("上传失败");
    }
    // 编辑器需要返回这样的格式
    // http://www.wangeditor.com/doc/pages/07-上传图片/01-配置服务端接口.html
    return Result::success($successList);
}

function delete($currentBackDir)
{
    $name = $_POST["key"];
    $path = $_POST["url"];
    // 实际保存目录
    $realPath = implode("/", [$_SERVER["DOCUMENT_ROOT"], $currentBackDir, $path]);
    if (!file_exists($realPath)) {
        return Result::error($name . "文件不存在");
    }
    if (!unlink($realPath)) {
        return Result::error("删除失败");
    }
    return Result::success();
}

function deleteVideo($currentBackDir)
{
    $request = json_decode(file_get_contents('php://input'), true);
    $data = $request["data"][0];
    // 实际保存目录
    $realPath = implode("/", [$_SERVER["DOCUMENT_ROOT"], $currentBackDir, $data["url"]]);
    if (!file_exists($realPath)) {
        return Result::error($data["name"] . "文件不存在");
    }
    if (!unlink($realPath)) {
        return Result::error("删除失败");
    }
    return Result::success();
}
