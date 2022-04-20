<?php

namespace App\Controller;

use App\Util\Util;
use App\Util\File;
use App\Model\LogModel;
use App\Core\BaseController;
use App\Core\Result\Result;

/**
 * UploadController
 */
class UploadController extends BaseController
{
    private $basePath;
    private $config;
    /**
     * @var LogModel
     */
    private $logModel;

    public function __construct(LogModel $logModel)
    {
        parent::__construct();
        $this->basePath = \App\DI()->config['basePath'];
        $this->config = \App\DI()->config['upload'];
        $this->logModel = $logModel;
    }

    /**
     * Download file
     * 
     * @param string filename
     * @param string type
     * @return File
     */
    public function download()
    {
        $filename = strval($this->request->get('filename'));
        $type = strval($this->request->get('type'));
        if (empty($filename) || empty($type)) {
            return Result::error('Filename or type does not exist.');
        }

        $filePath = implode('/', [$this->basePath, $this->config[$type]['localPath'], $filename]);
        $file = (new File())->setAbsolutePath($filePath);
        if (!$file->download()) {
            return Result::error('Download error.');
        }
    }

    /**
     * Upload file
     * 
     * @param string type
     * @param string targetDir
     * @param bool useTimeDir
     * @param bool useRandomName
     * @param bool overwrite
     * @param object reizeConfig     {enable: true/false, width: null, height: int}
     * @param object compressConfig  {enable: true/false, quality: 0-100}
     * @param object watermarkConfig {enable: true/false, path: '', x: 0, y: 0, position: top-left/top/top-right/left/center/right/bottom-left/bottom/bottom-right}
     * @return Result
     */
    public function add()
    {
        // upload file type
        $type = strval($this->request->get('type', 'image'));
        // target directory, like: /test/abc/
        $targetDir = strval($this->request->get('targetDir'));
        // use time directory
        $useTimeDir = $this->request->get('useTimeDir', false);
        // use random filename
        $useRandomName = $this->request->get('useRandomName', false);
        // overwrite file
        $overwrite = $this->request->get('overwrite', false);
        // reize config
        $reizeConfig = $this->request->get('reizeConfig');
        // compress config
        $compressConfig = $this->request->get('compressConfig');
        // watermark config
        $watermarkConfig = $this->request->get('watermarkConfig');

        \App\debug('useTimeDir', $useTimeDir);
        \App\debug('useRandomName', $useRandomName);
        \App\debug('overwrite', $overwrite);
        \App\debug('reizeConfig', $reizeConfig);
        \App\debug('compressConfig', $compressConfig);
        \App\debug('watermarkConfig', $watermarkConfig);

        // local saving directory
        $localUploadDir = implode('/', [$this->basePath, $this->config[$type]['localPath']]);

        // upload to target directory
        if (!empty($targetDir)) {
            $localUploadDir = implode('/', [$localUploadDir, $targetDir]);
        }
        // upload to time directory
        if (filter_var($useTimeDir, FILTER_VALIDATE_BOOLEAN)) {
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
                return Result::error(sprintf('File is too smaller: %d, min limit: %d', $fileSizeKB, $this->config[$type]['minKB']));
            }
            if ($this->config[$type]['maxKB'] < $fileSizeKB) {
                return Result::error(sprintf('File is too bigger: %d, max limit: %d', $fileSizeKB, $this->config[$type]['maxKB']));
            }

            // wrap file
            $file = (new File())->setAbsolutePath($fileTmp);
            // check file type
            if ($file->mimeType !== false && !in_array($file->mimeType, $this->config[$type]['allowMimeType'])) {
                return Result::error(sprintf('File type not allow: %d, allow type: %s', $file->mimeType, json_encode($this->config[$type]['allowMimeType'])));
            }

            // upload failed
            if (!is_uploaded_file($fileTmp)) {
                $this->logModel->asError(sprintf('Upload file failed: %s.', $fileNameWithExt));
                array_push($failList, $fileNameWithExt);
                continue;
            }

            // set local upload file path
            $localUploadFile = implode('/', [$localUploadDir, $fileNameWithExt]);

            // overwrite exist file?
            if (filter_var($overwrite, FILTER_VALIDATE_BOOLEAN)) {
                if (file_exists($localUploadFile)) {
                    // remove exist file
                    unlink($localUploadFile);
                }
            } else {
                // rename upload file
                while (file_exists($localUploadFile)) {
                    if (!filter_var($useRandomName, FILTER_VALIDATE_BOOLEAN)) {
                        return Result::error(sprintf('Filename already existed: %s. If you want to overwrite it, please post { overwrite: true }. If you do not, please post { useRandomName: true } to use random filename.', $fileNameWithExt));
                    }
                    // set random filename
                    // test.jpg => renamexxxxxx.jpg
                    $randomStr = Util::randomStr(15);
                    $fileNameWithExt = implode('.', [$randomStr, $file->fileExt]);
                    $localUploadFile = implode('/', [$localUploadDir, $fileNameWithExt]);
                }
            }
            // move temp file to local saving directory 
            if (move_uploaded_file($fileTmp, $localUploadFile)) {

                if ($type === 'image') {
                    // reize config
                    if ($reizeConfig && filter_var($reizeConfig['enable'], FILTER_VALIDATE_BOOLEAN)) {
                        if ($reizeConfig['width'] || $reizeConfig['height']) {
                            \App\DI()->image
                                ->make($localUploadFile)
                                ->resize($reizeConfig['width'], $reizeConfig['height'])
                                ->save();
                        } else {
                            return Result::error('ReizeConfig width or height does not exist.');
                        }
                    }
                    // compress config
                    if ($compressConfig && filter_var($compressConfig['enable'], FILTER_VALIDATE_BOOLEAN) && ($file->mimeType === 'image/jpeg' || $file->mimeType === 'image/png')) {
                        $compressConfig['quality'] = isset($compressConfig['quality']) ? intval($compressConfig['quality']) : $this->config[$type]['compressConfig']['quality'];
                        \App\DI()->image
                            ->make($localUploadFile)
                            ->encode('jpg', $compressConfig['quality'])
                            ->save();
                        $fileNameWithExt = File::rewriteType($fileNameWithExt, 'jpg');
                    }
                    // watermark config
                    // fixme:not work
                    if ($watermarkConfig && filter_var($watermarkConfig['enable'], FILTER_VALIDATE_BOOLEAN)) {
                        $watermarkConfig['path'] = isset($watermarkConfig['path']) ? $watermarkConfig['path'] : $this->config[$type]['watermarkConfig']['path'];
                        $watermarkConfig['position'] = isset($watermarkConfig['position']) ? $watermarkConfig['position'] : $this->config[$type]['watermarkConfig']['position'];
                        $watermarkConfig['x'] = isset($watermarkConfig['x']) ? intval($watermarkConfig['x']) : $this->config[$type]['watermarkConfig']['x'];
                        $watermarkConfig['y'] = isset($watermarkConfig['y']) ? intval($watermarkConfig['y']) : $this->config[$type]['watermarkConfig']['y'];
                        \App\DI()->image
                            ->make($localUploadFile)
                            ->insert($watermarkConfig['path'], $watermarkConfig['position'], $watermarkConfig['x'], $watermarkConfig['y'])
                            ->save();
                    }
                }

                $data = [
                    'name' => $fileNameWithExt,
                    // splice visit url
                    'url' => sprintf('%s?filename=%s&type=%s', $this->config['downloadUrl'], $fileNameWithExt, $type),
                ];
                $this->logModel->asInfo(sprintf('Upload file success: %s.', $fileNameWithExt));
                array_push($successList, $data);
            }
        }

        if (count($successList) === 0) {
            return Result::error(sprintf('Upload failed: %s', json_encode($failList)));
        }
        return Result::success($successList);
    }

    /**
     * Remove file
     * 
     * @param string filename
     * @param string type
     * @return Result
     */
    public function remove()
    {
        $filename = strval($this->request->get('filename'));
        $type = strval($this->request->get('type'));
        if (empty($filename) || empty($type)) {
            return Result::error('Filename or type does not exist.');
        }

        $localUploadFile = implode('/', [$this->basePath, $this->config[$type]['localPath'], $filename]);
        if (!file_exists($localUploadFile)) {
            return Result::error('File does not exist.');
        }
        if (!unlink($localUploadFile)) {
            $this->logModel->asError(sprintf('Remove file failed: %s.', $filename));
            return Result::error('Remove failed.');
        }
        $this->logModel->asInfo(sprintf('Remove file success: %s.', $filename));
        return Result::success();
    }
}
