<?php

require_once __DIR__ . '/Store.php';

class Disk
{
    /** @var FileStore */
    private $store;

    public function __construct($rootDir = '')
    {
        $this->store = new FileStore($rootDir);
    }

    public function setRootDir($rootDir)
    {
        $this->store->setRootDir($rootDir);
    }

    public function setDir($subDir)
    {
        return rtrim($this->store->directory($subDir), '/') . '/';
    }

    public function upload(File $file)
    {
        return $this->store->store($file)->toArray();
    }

    public function uploadMany(array $files)
    {
        return $this->store->storeMany($files);
    }
}
