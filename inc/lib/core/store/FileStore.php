<?php

require_once __DIR__ . '/FileStoreInterface.php';

final class FileStore implements FileStoreInterface
{
    /** @var string */
    private $rootDir;

    public function __construct($rootDir = '')
    {
        $defaultRoot = defined('ROOT') ? rtrim(ROOT, '/\\') . '/uploads' : dirname(__DIR__, 4) . '/uploads';
        $this->setRootDir($rootDir !== '' ? $rootDir : $defaultRoot);
    }

    public function setRootDir($rootDir)
    {
        $normalized = rtrim(str_replace('\\', '/', (string) $rootDir), '/');

        if ($normalized === '') {
            throw new RuntimeException('스토리지 디렉터리가 비어 있습니다.');
        }

        // 디렉터리 생성은 쓰기 시점(directory())으로 미룬다 — 읽기 전용 사용(다운로드)이
        // 권한 문제로 실패하지 않도록 생성자에서는 경로만 보관한다.
        $this->rootDir = $normalized;
    }

    public function getRootDir()
    {
        return $this->rootDir;
    }

    public function directory($subDir = '')
    {
        $directory = $this->directoryPath($subDir);

        $this->ensureDirectory($directory);

        return $directory;
    }

    /** 경로 계산만 — 디렉터리를 생성하지 않는다 (읽기 전용 경로용). */
    private function directoryPath($subDir = '')
    {
        $directory = $this->rootDir;

        if ($subDir !== '') {
            $directory .= '/' . trim(str_replace('\\', '/', (string) $subDir), '/');
        }

        return $directory;
    }

    public function ensureDirectory($directory)
    {
        $directory = rtrim(str_replace('\\', '/', (string) $directory), '/');

        if ($directory === '') {
            throw new RuntimeException('스토리지 디렉터리가 비어 있습니다.');
        }

        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException('스토리지 디렉터리를 생성할 수 없습니다.');
        }
    }

    public function store(UploadedFile $file, $subDir = '')
    {
        if (!$file->isValid()) {
            throw new InvalidArgumentException('유효하지 않은 업로드 파일입니다.');
        }

        $directory = $this->directory($subDir);
        $storedName = $this->createStoredName((string) $file->getOriginalName());
        $targetPath = $directory . '/' . $storedName;
        $tmpName = (string) $file->getTmpName();

        $moved = function_exists('is_uploaded_file') && is_uploaded_file($tmpName)
            ? move_uploaded_file($tmpName, $targetPath)
            : rename($tmpName, $targetPath);

        if (!$moved) {
            throw new RuntimeException('업로드 파일 저장에 실패했습니다.');
        }

        return new StoredFile(
            (string) $file->getOriginalName(),
            $storedName,
            $directory,
            (string) $file->getMimeType(),
            (int) $file->getSize()
        );
    }

    public function storeMany(array $files, $subDir = '')
    {
        $results = [];

        foreach ((array) ($files['name'] ?? []) as $index => $name) {
            $file = new UploadedFile(null);
            $file->setEntity([
                'name' => $files['name'][$index] ?? '',
                'tmp_name' => $files['tmp_name'][$index] ?? '',
                'size' => $files['size'][$index] ?? 0,
                'type' => $files['type'][$index] ?? '',
                'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
            ]);

            try {
                $results[] = $this->store($file, $subDir)->toArray();
            } catch (Throwable $e) {
                $results[] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    public function resolveFilePath(string $storedName, string $subDir = ''): ?string
    {
        $safeName = basename(str_replace('\\', '/', $storedName));
        if ($safeName === '') {
            return null;
        }

        $path = $this->directoryPath($subDir) . '/' . $safeName;

        return is_file($path) ? $path : null;
    }

    public function deleteByStoredName($storedName, $subDir = '')
    {
        $path = $this->resolveFilePath($storedName, $subDir);

        return $path !== null ? $this->deletePath($path) : false;
    }

    public function deletePath($path)
    {
        return $path !== '' && is_file($path) ? unlink($path) : false;
    }

    private function createStoredName($originalName)
    {
        $extension = strtolower((string) pathinfo((string) $originalName, PATHINFO_EXTENSION));
        $unique = str_replace('.', '', uniqid('', true));

        return $extension !== '' ? $unique . '.' . $extension : $unique;
    }
}
