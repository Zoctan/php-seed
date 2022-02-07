<?php

namespace App\Controller;

use App\Util\Util;
use App\Core\BaseController;
use App\Core\Response\ResultGenerator;

class UploadController extends BaseController
{
    // 当前后端文件夹目录
    private $currentBackDir = "api/";

    function create()
    {
        $dir = $this->request->get("dir");
        // 按时间保存
        $uploadDir = date("Y-m-d", time());
        // 实际保存目录
        $realUploadDir = implode("/", [$_SERVER["DOCUMENT_ROOT"], $this->currentBackDir, $uploadDir]);

        // 上传到子文件夹
        if (!empty($dir)) {
            $realUploadDir .= $dir . "/";
            $uploadDir .= $dir . "/";
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
            return ResultGenerator::errorWithMsg("上传失败");
        }
        // 编辑器需要返回这样的格式
        // http://www.wangeditor.com/doc/pages/07-上传图片/01-配置服务端接口.html
        return ResultGenerator::successWithData($successList);
    }

    function delete()
    {
        $name = $_POST["key"];
        $path = $_POST["url"];
        // 实际保存目录
        $realPath = implode("/", [$_SERVER["DOCUMENT_ROOT"], $this->currentBackDir, $path]);
        if (!file_exists($realPath)) {
            return ResultGenerator::errorWithMsg($name . "文件不存在");
        }
        if (!unlink($realPath)) {
            return ResultGenerator::errorWithMsg("删除失败");
        }
        return ResultGenerator::success();
    }

    function deleteVideo()
    {
        $request = json_decode(file_get_contents('php://input'), true);
        $data = $request["data"][0];
        // 实际保存目录
        $realPath = implode("/", [$_SERVER["DOCUMENT_ROOT"], $this->currentBackDir, $data["url"]]);
        if (!file_exists($realPath)) {
            return ResultGenerator::errorWithMsg($data["name"] . "文件不存在");
        }
        if (!unlink($realPath)) {
            return ResultGenerator::errorWithMsg("删除失败");
        }
        return ResultGenerator::success();
    }
}
