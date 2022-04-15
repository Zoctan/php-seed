<?php

namespace App\Util;

class File
{
  public $basePath;
  public $absolutePath;
  public $relativePath;
  public $fileNameWithExt;
  public $fileNameWithoutExt;
  public $fileExt;

  public function __construct($path, $basePath = __DIR__)
  {
    $this->basePath = $basePath;
    if (strpos($this->basePath, $path) !== false) {
      $this->absolutePath = $path;
      $this->relativePath = $this->getRelativePath($this->absolutePath);
    } else {
      $this->absolutePath = $this->getAbsolutePath($path);
      $this->relativePath = $path;
    }
    $this->setFileInfo();
  }

  private function setFileInfo()
  {
    // C:\dir\test.txt
    $fileInfo = pathinfo($this->absolutePath);
    // C:\dir
    // $this->absolutePath = $fileInfo['dirname'];
    // test.txt
    $this->fileNameWithExt = $fileInfo['basename'];
    // test
    $this->fileNameWithoutExt = $fileInfo['filename'];
    // txt
    $this->fileExt = $fileInfo['extension'];
  }

  private function getAbsolutePath($relativePath)
  {
    return implode('/', [$this->basePath, $relativePath]);
  }

  private function getRelativePath($absolutePath)
  {
    return str_replace($this->basePath, '', $absolutePath);
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
