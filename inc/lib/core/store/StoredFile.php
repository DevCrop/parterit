<?php

final class StoredFile
{
    /** @var string */
    private $originalName;

    /** @var string */
    private $storedName;

    /** @var string */
    private $directory;

    /** @var string */
    private $mimeType;

    /** @var int */
    private $size;

    public function __construct($originalName, $storedName, $directory, $mimeType, $size)
    {
        $this->originalName = (string) $originalName;
        $this->storedName = (string) $storedName;
        $this->directory = rtrim(str_replace('\\', '/', (string) $directory), '/');
        $this->mimeType = (string) $mimeType;
        $this->size = (int) $size;
    }

    public function getOriginalName()
    {
        return $this->originalName;
    }

    public function getStoredName()
    {
        return $this->storedName;
    }

    public function getDirectory()
    {
        return $this->directory;
    }

    public function getMimeType()
    {
        return $this->mimeType;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getAbsolutePath()
    {
        return $this->directory . '/' . $this->storedName;
    }

    public function toArray()
    {
        return [
            'success' => true,
            'original_name' => $this->getOriginalName(),
            'stored_name' => $this->getStoredName(),
            'directory' => $this->getDirectory(),
            'mime_type' => $this->getMimeType(),
            'size' => $this->getSize(),
        ];
    }
}
