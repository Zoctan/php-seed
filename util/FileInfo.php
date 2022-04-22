<?php

namespace App\Util;

class FileInfo
{
    public $absolutePath;
    public $mimeType;
    public $fileExt;
    public $basePath;
    public $fileSize;
    public $fileNameWithExt;
    public $fileNameWithoutExt;

    public function __construct($absolutePath, $checkExist = false)
    {
        $this->absolutePath = self::platformSlashes($absolutePath);
        if ($checkExist && !$this->exists()) {
            throw new \Exception('File does not exist');
        }

        $this->mimeType = self::getMimeType($absolutePath);
        $this->fileSize = filesize($absolutePath);

        // C:\\dir\demo\x.test.txt
        // /dir/demo/x.test.txt
        $fileInfo = pathinfo($absolutePath);
        // txt
        $this->fileExt = $fileInfo['extension'];
        // C:\\dir\demo
        // /dir/demo
        $this->basePath = self::platformSlashes($fileInfo['dirname']);
        // x.test.txt
        $this->fileNameWithExt = $fileInfo['basename'];
        // x.test
        $this->fileNameWithoutExt = $fileInfo['filename'];
    }

    public function exists()
    {
        return file_exists($this->absolutePath);
    }

    public static function getMimeType($absolutePath)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (!$finfo) {
            return null;
        }
        $mimeType = finfo_file($finfo, $absolutePath);
        finfo_close($finfo);
        if (!$mimeType) {
            return null;
        }
        return $mimeType;
    }

    public static function getExt($path)
    {
        $fileInfo = pathinfo($path);
        return $fileInfo['extension'];
    }

    public static function rewriteType($path, $fileExt)
    {
        $fileInfo = pathinfo($path);
        $fileNameWithExt = implode('.', [$fileInfo['filename'], $fileExt]);
        if ($fileInfo['dirname'] !== '.') {
            return implode('/', [$fileInfo['dirname'], $fileNameWithExt]);
        }
        return $fileNameWithExt;
    }

    public static function platformSlashes($path)
    {
        return str_replace(['/', '\\'], '/', $path);
    }
}
