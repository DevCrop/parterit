<?php
declare(strict_types=1);

require_once __DIR__ . '/Store.php';
require_once __DIR__ . '/FileDownloadServiceInterface.php';
require_once __DIR__ . '/DownloadException.php';

final class BoardFileDownloadService implements FileDownloadServiceInterface
{
    private const FIELD_MAP = [
        'thumb'   => ['stored' => 'thumb_image',         'original' => 'thumb_image'],
        'attach1' => ['stored' => 'file_attach_1',       'original' => 'file_attach_origin_1'],
        'attach2' => ['stored' => 'file_attach_2',       'original' => 'file_attach_origin_2'],
        'attach3' => ['stored' => 'file_attach_3',       'original' => 'file_attach_origin_3'],
        'attach4' => ['stored' => 'file_attach_4',       'original' => 'file_attach_origin_4'],
        'attach5' => ['stored' => 'file_attach_5',       'original' => 'file_attach_origin_5'],
    ];

    private \PDO $db;
    private FileStoreInterface $store;

    public function __construct(\PDO $db, FileStoreInterface $store)
    {
        $this->db    = $db;
        $this->store = $store;
    }

    public function download(int $postId, string $fieldKey): void
    {
        if ($postId <= 0) {
            throw new DownloadException('잘못된 요청입니다. (no)', 400);
        }

        $field = self::FIELD_MAP[$fieldKey] ?? null;
        if ($field === null) {
            throw new DownloadException('유효하지 않은 파일 요청입니다.', 400);
        }

        $record = $this->findBoardFileRecord($postId);
        if ($record === null) {
            throw new DownloadException('게시글 정보를 찾을 수 없습니다.', 404);
        }

        $storedName   = trim((string) ($record[$field['stored']]   ?? ''));
        $originalName = trim((string) ($record[$field['original']] ?? ''));

        if ($storedName === '') {
            throw new DownloadException('요청한 파일이 없습니다.', 404);
        }

        $filePath = $this->resolvePath($storedName);
        if ($filePath === null) {
            throw new DownloadException('파일을 찾을 수 없습니다.', 404);
        }

        if ($originalName === '') {
            $originalName = basename($storedName);
        }

        $fileSize = filesize($filePath);
        if ($fileSize === false) {
            throw new DownloadException('파일 정보를 읽을 수 없습니다.', 500);
        }

        $this->sendHeaders(
            $this->sanitizeDownloadName($originalName),
            $this->detectMimeType($filePath),
            $fileSize
        );

        $this->streamFile($filePath);
    }

    private function findBoardFileRecord(int $postId): ?array
    {
        $statement = $this->db->prepare(
            'SELECT thumb_image,
                    file_attach_1, file_attach_2, file_attach_3, file_attach_4, file_attach_5,
                    file_attach_origin_1, file_attach_origin_2, file_attach_origin_3, file_attach_origin_4, file_attach_origin_5
             FROM nb_board
             WHERE no = :no
             LIMIT 1'
        );
        $statement->execute([':no' => $postId]);

        $record = $statement->fetch(\PDO::FETCH_ASSOC);
        return is_array($record) ? $record : null;
    }

    private function resolvePath(string $storedName): ?string
    {
        $safeName = basename(str_replace('\\', '/', $storedName));
        return $safeName !== '' ? $this->store->resolveFilePath($safeName) : null;
    }

    private function sanitizeDownloadName(string $filename): string
    {
        $filename = trim(str_replace(["\r", "\n"], '', $filename));
        return $filename !== '' ? $filename : 'download';
    }

    private function detectMimeType(string $filePath): string
    {
        if (class_exists('finfo')) {
            $finfo    = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($filePath);
            if (is_string($mimeType) && $mimeType !== '') {
                return $mimeType;
            }
        }

        return 'application/octet-stream';
    }

    private function sendHeaders(string $filename, string $mimeType, int $fileSize): void
    {
        if (headers_sent()) {
            throw new DownloadException('헤더를 이미 전송했습니다.', 500);
        }

        $asciiFallback   = preg_replace('/[^A-Za-z0-9._-]/', '_', $filename) ?: 'download';
        $encodedFilename = rawurlencode($filename);

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $asciiFallback . '"; filename*=UTF-8\'\'' . $encodedFilename);
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $fileSize);
        header('Cache-Control: private, no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('X-Content-Type-Options: nosniff');
    }

    private function streamFile(string $filePath): void
    {
        while (function_exists('ob_get_level') && ob_get_level() > 0) {
            ob_end_clean();
        }

        $handle = fopen($filePath, 'rb');
        if ($handle === false) {
            throw new DownloadException('파일 스트림을 열 수 없습니다.', 500);
        }

        try {
            while (!feof($handle)) {
                $chunk = fread($handle, 8192);
                if ($chunk === false) {
                    break;
                }
                echo $chunk;
            }
        } finally {
            fclose($handle);
        }
    }
}
