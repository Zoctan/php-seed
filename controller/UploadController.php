<?php

namespace App\Controller;

use App\Util\Util;
use App\Util\FileUtil;
use App\Core\BaseController;
use App\Core\Response\ResultGenerator;

class UploadController extends BaseController
{
    private $basePath;
    private $baseUrl;
    private $config;

    public function __construct()
    {
        parent::__construct();
        $this->basePath = \App\DI()->config['basePath'];
        $this->baseUrl = \App\DI()->config['app']['baseUrl'];
        $this->config = \App\DI()->config['app']['upload'];
    }

    public function download()
    {
        $filename = strval($this->request->get('filename'));
        $type = strval($this->request->get('type'));
        if (empty($filename) || empty($type)) {
            return ResultGenerator::errorWithMsg('filename or type does not exist');
        }
        $filePath = implode('/', [$this->config[$type]['localPath'], $filename]);
        $file = new FileUtil($filePath, $this->basePath);
        if (!$file->download()) {
            return ResultGenerator::errorWithMsg('download error');
        }
    }

    public function add()
    {
        // 目标文件夹：/image/xx/
        $targetDir = strval($this->request->get('targetDir'));
        // 是否使用时间文件夹
        $useTimeDir = boolval($this->request->get('useTimeDir', false));
        // 上传的文件类型
        $type = strval($this->request->get('type', 'image'));
        // 是否使用随机文件名
        $useRandomName = boolval($this->request->get('useRandomName', false));
        // 是否覆盖已有文件
        $replace = boolval($this->request->get('replace', false));

        // 实际保存目录
        $realUploadDir = implode('/', [$this->basePath, $this->config[$type]['localPath']]);
        // 网络访问地址
        $uploadUrl = implode('/', [$this->baseUrl, $this->config[$type]['localPath']]);
        // 上传到目标文件夹
        if (!empty($targetDir)) {
            $realUploadDir = implode('/', [$realUploadDir, $targetDir]);
        }
        // 按时间保存
        if ($useTimeDir) {
            $realUploadDir = implode('/', [$realUploadDir, date('Y-m-d')]);
        }
        // 不存在则创建
        if (!is_dir($realUploadDir)) {
            mkdir($realUploadDir, 0777, true);
        }

        // 成功上传的文件列表
        $successList = [];
        // 失败上传的文件列表
        $failList = [];
        foreach ($_FILES as $item) {
            // 文件名称
            $fileNameWithExt = $item['name'];
            // 文件类型
            $fileType = $item['type'];
            // 文件大小
            $fileSizeByte = $item['size'];
            $fileSizeKB = $fileSizeByte / 1024;
            // 文件的临时保存路径
            $fileTmp = $item['tmp_name'];

            if ($this->config[$type]['minKB'] > $fileSizeKB) {
                return ResultGenerator::errorWithMsg('File is too smaller: ' . $fileSizeKB . ', min limit: ' . $this->config[$type]['minKB']);
            }
            if ($this->config[$type]['maxKB'] < $fileSizeKB) {
                return ResultGenerator::errorWithMsg('File is too bigger: ' . $fileSizeKB . ', max limit: ' . $this->config[$type]['maxKB']);
            }
            if (!in_array($fileType, $this->config[$type]['allowType'])) {
                return ResultGenerator::errorWithMsg('File type not allow: ' . $fileType . ', allow type: ' . json_encode($this->config[$type]['allowType']));
            }

            $isUploadSuccess = is_uploaded_file($fileTmp);
            // 上传失败
            if (!$isUploadSuccess) {
                array_push($failList, $fileNameWithExt);
                continue;
            }

            $fileNameArray = explode('.', $fileNameWithExt);
            $fileExt = array_pop($fileNameArray);
            $fileNameWithoutExt = implode('.', $fileNameArray);
            $realUploadFile = implode('/', [$realUploadDir, $fileNameWithExt]);
            // 覆盖已有文件
            if ($replace) {
                if (file_exists($realUploadFile)) {
                    unlink($realUploadFile);
                }
            } else {
                while (file_exists($realUploadFile)) {
                    if (!$useRandomName) {
                        return ResultGenerator::errorWithMsg(sprintf('File name already existed: %s. If you want to replace it, please post { replace: true }. If you do not, please post { useRandomName: true } to use random file name.', $fileNameWithExt));
                    }
                    // set random file name
                    // test.jpg -> asdfghjkl.jpg
                    $randomStr = Util::randomStr(15);
                    $fileNameWithExt = implode('.', [$randomStr, $fileExt]);
                    $realUploadFile = implode('/', [$realUploadDir, $fileNameWithExt]);
                }
            }

            // 返回的应该是网站目录下的上传目录，而不是D:\xx这样的目录地址
            $uploadFileUrl = sprintf('%s?file=%s', $uploadUrl, $fileNameWithExt);

            $isMoveSuccess = move_uploaded_file($fileTmp, $realUploadFile);
            if ($isMoveSuccess) {
                $data = [
                    'name' => $fileNameWithExt,
                    'url' => $uploadFileUrl,
                ];
                array_push($successList, $data);
            }
        }

        if (count($successList) === 0) {
            return ResultGenerator::errorWithMsg('upload failed: ' . json_encode($failList));
        }
        return ResultGenerator::successWithData($successList);
    }

    public function delete()
    {
        $filename = strval($this->request->get('filename'));
        $type = strval($this->request->get('type'));
        if (empty($filename) || empty($type)) {
            return ResultGenerator::errorWithMsg('filename or type does not exist');
        }

        $realUploadFile = str_replace($this->baseUrl, $this->basePath, $filename);
        if (!file_exists($realUploadFile)) {
            return ResultGenerator::errorWithMsg('File is not existed.');
        }
        if (!unlink($realUploadFile)) {
            return ResultGenerator::errorWithMsg('delete failed');
        }
        return ResultGenerator::success();
    }
}
