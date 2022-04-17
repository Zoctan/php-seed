<?php

namespace App\Util;

class File
{
    public $basePath;
    private $absolutePath;
    private $relativePath;
    public $fileNameWithExt;
    public $fileNameWithoutExt;
    public $fileExt;
    public $mimeType;

    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
        return $this;
    }

    public function setAbsolutePath($absolutePath)
    {
        $this->absolutePath = $absolutePath;
        $this->init();
        $this->relativePath = $this->getRelativePath($this->absolutePath);
        return $this;
    }

    public function setRelativePath($relativePath)
    {
        $this->relativePath = ltrim($relativePath, '/');
        $this->init();
        $this->absolutePath = $this->getAbsolutePath($this->absolutePath);
        return $this;
    }

    public function getAbsolutePath()
    {
        if ($this->absolutePath) {
            return $this->absolutePath;
        }
        if (empty($this->basePath) || empty($this->relativePath)) {
            throw new \Exception('Set base path and relative path first, or set absolute path first');
        }
        return implode('/', [$this->basePath, $this->relativePath]);
    }

    public function getRelativePath()
    {
        if ($this->relativePath) {
            return $this->relativePath;
        }
        if (empty($this->basePath) || empty($this->absolutePath)) {
            throw new \Exception('Set base path and absolute path first, or set relative path first');
        }
        return str_replace($this->basePath, '', $this->absolutePath);
    }


    private function init()
    {
        $this->setFileInfo();
        $this->mimeType = self::getMimeType($this->absolutePath);
    }

    private function setFileInfo()
    {
        // C:\\dir\demo\x.test.txt
        // /dir/demo/x.test.txt
        $fileInfo = pathinfo($this->absolutePath);
        // C:\\dir\demo
        // /dir/demo
        if (empty($this->basePath)) {
            $this->basePath = $fileInfo['dirname'];
        }
        // x.test.txt
        $this->fileNameWithExt = $fileInfo['basename'];
        // x.test
        $this->fileNameWithoutExt = $fileInfo['filename'];
        // txt
        $this->fileExt = $fileInfo['extension'];
    }

    public static function getMimeType($absolutePath)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $absolutePath);
        finfo_close($finfo);
        return $mimeType;
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

    public function download()
    {
        // \App\debug('download', $this->absolutePath);
        if (!file_exists($this->absolutePath)) {
            header('HTTP/1.1 404 NOT FOUND');
            return false;
        } else {
            $file = fopen($this->absolutePath, 'rb');
            header('Content-type: application/octet-stream');
            header('Accept-Ranges: bytes');
            header('Accept-Length: ' . filesize($this->absolutePath));
            header('Content-Disposition: attachment; filename=' . $this->fileNameWithExt);
            echo fread($file, filesize($this->absolutePath));
            fclose($file);
            return true;
        }
    }
}
