<?php

class UploadedFile
{
    /** @var int */
    public static $MAX_SIZE = 10485760;

    public const EXTENSION_IMAGE = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
    public const EXTENSION_DOCUMENT = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf'];
    public const EXTENSION_ARCHIVE = ['zip', 'rar', '7z', 'tar', 'gz', 'iso', 'dmg'];
    public const EXTENSION_AUDIO = ['mp3', 'wav', 'ogg', 'flac', 'aac'];
    public const EXTENSION_VIDEO = ['mp4', 'avi', 'mov', 'mkv', 'wmv', 'webm'];
    public const EXTENSION_DATA = ['csv', 'json', 'xml'];

    /** @var string|null */
    protected $key;

    /** @var array|null */
    protected $entity;

    /** @var array */
    protected $extensions;

    public function __construct($key = null, array $extensions = [])
    {
        $this->key = $key;
        $this->entity = $key !== null && isset($_FILES[$key]) ? $_FILES[$key] : null;
        $this->extensions = !empty($extensions) ? array_values(array_unique(array_map('strtolower', $extensions))) : self::getAllExtensions();
    }

    public static function getAllExtensions()
    {
        return array_merge(
            self::EXTENSION_IMAGE,
            self::EXTENSION_DOCUMENT,
            self::EXTENSION_ARCHIVE,
            self::EXTENSION_AUDIO,
            self::EXTENSION_VIDEO,
            self::EXTENSION_DATA
        );
    }

    public function setEntity(array $fileData)
    {
        $this->entity = $fileData;
    }

    public function has()
    {
        return is_array($this->entity);
    }

    public function empty()
    {
        return !$this->has() || ((int) ($this->entity['size'] ?? 0) === 0);
    }

    public function hasUploadError()
    {
        return $this->has() && (int) ($this->entity['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK;
    }

    public function isExceedMaximumSize()
    {
        return $this->has() && (int) ($this->entity['size'] ?? 0) > (int) self::$MAX_SIZE;
    }

    public function isValidExtension()
    {
        if (!$this->has()) {
            return false;
        }

        $extension = strtolower((string) pathinfo((string) ($this->entity['name'] ?? ''), PATHINFO_EXTENSION));

        return $extension !== '' && in_array($extension, $this->extensions, true);
    }

    public function isUploadedFile()
    {
        $tmpName = $this->getTmpName();
        if ($tmpName === null || $tmpName === '') {
            return false;
        }

        if (function_exists('is_uploaded_file') && is_uploaded_file($tmpName)) {
            return true;
        }

        return is_file($tmpName);
    }

    public function isValid()
    {
        return $this->has()
            && !$this->empty()
            && !$this->hasUploadError()
            && !$this->isExceedMaximumSize()
            && $this->isValidExtension()
            && $this->isUploadedFile();
    }

    public function getOriginalName()
    {
        return $this->entity['name'] ?? null;
    }

    public function getMimeType()
    {
        return (string) ($this->entity['type'] ?? '');
    }

    public function getSize()
    {
        return (int) ($this->entity['size'] ?? 0);
    }

    public function getTmpName()
    {
        return $this->entity['tmp_name'] ?? null;
    }

    public function getError()
    {
        return (int) ($this->entity['error'] ?? UPLOAD_ERR_NO_FILE);
    }
}
