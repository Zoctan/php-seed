<?php

namespace App\Controller;

use App\Util\Util;
use App\Util\File;
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
        $file = new File($filePath, $this->basePath);
        if (!$file->download()) {
            return ResultGenerator::errorWithMsg('download error');
        }
    }

    public function add()
    {
        // target directory, like: /test/abc/
        $targetDir = strval($this->request->get('targetDir'));
        // use time directory
        $useTimeDir = boolval($this->request->get('useTimeDir', false));
        // use random file name
        $useRandomName = boolval($this->request->get('useRandomName', false));
        // upload file type
        $type = strval($this->request->get('type', 'image'));
        // overwrite file
        $overwrite = boolval($this->request->get('overwrite', false));

        // local saving directory
        $localUploadDir = implode('/', [$this->basePath, $this->config[$type]['localPath']]);
        // network visit url
        $uploadUrl = implode('/', [$this->baseUrl, $this->config[$type]['localPath']]);

        // upload to target directory
        if (!empty($targetDir)) {
            $localUploadDir = implode('/', [$localUploadDir, $targetDir]);
        }
        // upload to time directory
        if ($useTimeDir) {
            $localUploadDir = implode('/', [$localUploadDir, date('Y-m-d')]);
        }
        // create local saving directory if it does not exist
        if (!is_dir($localUploadDir)) {
            mkdir($localUploadDir, 0777, true);
        }

        // upload success file list
        $successList = [];
        // upload fail file list
        $failList = [];
        foreach ($_FILES as $item) {
            // filename with extension
            $fileNameWithExt = $item['name'];
            // file type
            $fileType = $item['type'];
            // file size in byte
            $fileSizeByte = $item['size'];
            $fileSizeKB = $fileSizeByte / 1024;
            // temp file path in local disk
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

            // upload fail
            if (!is_uploaded_file($fileTmp)) {
                array_push($failList, $fileNameWithExt);
                continue;
            }

            $fileNameArray = explode('.', $fileNameWithExt);
            $fileExt = array_pop($fileNameArray);
            $fileNameWithoutExt = implode('.', $fileNameArray);
            $localUploadFile = implode('/', [$localUploadDir, $fileNameWithExt]);

            // overwrite exist file?
            if ($overwrite) {
                if (file_exists($localUploadFile)) {
                    // remove exist file
                    unlink($localUploadFile);
                }
            } else {
                // rename upload file
                while (file_exists($localUploadFile)) {
                    if (!$useRandomName) {
                        return ResultGenerator::errorWithMsg(sprintf('File name already existed: %s. If you want to replace it, please post { replace: true }. If you do not, please post { useRandomName: true } to use random file name.', $fileNameWithExt));
                    }
                    // set random filename
                    // test.jpg -> asdfghjkl.jpg
                    $randomStr = Util::randomStr(15);
                    $fileNameWithExt = implode('.', [$randomStr, $fileExt]);
                    $localUploadFile = implode('/', [$localUploadDir, $fileNameWithExt]);
                }
            }

            // move temp file to local saving directory 
            if (move_uploaded_file($fileTmp, $localUploadFile)) {
                $data = [
                    'name' => $fileNameWithExt,
                    // splice visit url
                    'url' => sprintf('%s?file=%s', $uploadUrl, $fileNameWithExt),
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

        $localUploadFile = str_replace($this->baseUrl, $this->basePath, $filename);
        if (!file_exists($localUploadFile)) {
            return ResultGenerator::errorWithMsg('file does not exist');
        }
        if (!unlink($localUploadFile)) {
            return ResultGenerator::errorWithMsg('delete failed');
        }
        return ResultGenerator::success();
    }
}
