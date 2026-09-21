<?php

include_once "../../../bootstrap/admin.php";
include_once "../../../lib/admin.check.ajax.php";
require_once "../../../../inc/lib/core/Store.php";

$role->requireLogin();
$role->requireCanModify();

$root = rtrim($NO_PROJECT_ROOT, '/\\');
$uploadDir = $UPLOAD_DIR_BOARD;
$uploadBaseDir = $UPLOAD_WDIR_BOARD;
$maxFileSize = 20 * 1024 * 1024;
$store = new FileStore($uploadDir);

$allowedExtensions = [
    'jpg', 'jpeg', 'png', 'gif', 'webp',
    'pdf', 'zip',
    'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'hwp',
    'txt', 'mp4', 'mov', 'avi',
];

$method = strtolower((string) ($_POST['_method'] ?? $_SERVER['REQUEST_METHOD'] ?? 'post'));
$response = ['success' => false, 'filename' => null];

if (is_dir($uploadDir) && !is_writable($uploadDir)) {
    echo json_encode(['success' => false, 'filename' => null, 'error' => '업로드 경로 권한 오류']);
    exit;
}

if ($method === 'post') {
    $requestedExtension = strtolower(trim((string) ($_POST['extension'] ?? '')));

    if (!isset($_FILES['file']) || (int) ($_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        echo json_encode([
            'success' => false,
            'filename' => null,
            'error' => isset($_FILES['file']) ? 'File upload error' : '파일이 없습니다.',
        ]);
        exit;
    }

    $file = new UploadedFile('file', $allowedExtensions);
    $realExtension = strtolower((string) pathinfo((string) $file->getOriginalName(), PATHINFO_EXTENSION));
    $fileSize = $file->getSize();
    $tmpName = (string) $file->getTmpName();

    if ($requestedExtension === '' || !in_array($requestedExtension, $allowedExtensions, true)) {
        $response = ['success' => false, 'filename' => null, 'error' => '허용되지 않는 확장자입니다.'];
    } elseif ($realExtension !== $requestedExtension || !$file->isValidExtension()) {
        $response = ['success' => false, 'filename' => null, 'error' => '파일 확장자가 일치하지 않거나 허용되지 않습니다.'];
    } elseif ($fileSize <= 0 || $fileSize > $maxFileSize || $file->isExceedMaximumSize()) {
        $response = ['success' => false, 'filename' => null, 'error' => '파일 크기 제한을 초과했습니다.'];
    } elseif (!$file->isUploadedFile()) {
        $response = ['success' => false, 'filename' => null, 'error' => '유효한 업로드 파일이 아닙니다.'];
    } else {
        $mimeType = '';
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo !== false) {
                $mimeType = (string) finfo_file($finfo, $tmpName);
                finfo_close($finfo);
            }
        } elseif (function_exists('mime_content_type')) {
            $mimeType = (string) mime_content_type($tmpName);
        }

        $blockedMimeKeywords = ['php', 'html', 'javascript', 'x-sh', 'x-httpd'];
        $isBlockedMime = false;
        foreach ($blockedMimeKeywords as $keyword) {
            if ($mimeType !== '' && stripos($mimeType, $keyword) !== false) {
                $isBlockedMime = true;
                break;
            }
        }

        if ($isBlockedMime) {
            $response = ['success' => false, 'filename' => null, 'error' => '허용되지 않는 파일 형식입니다.'];
        } else {
            try {
                $storedFile = $store->store($file);
                $response = [
                    'success' => true,
                    'filename' => $uploadBaseDir . '/' . $storedFile->getStoredName(),
                ];
            } catch (Throwable $e) {
                $response = ['success' => false, 'filename' => null, 'error' => '업로드 실패'];
            }
        }
    }
}

if ($method === 'delete') {
    $link = trim((string) ($_POST['link'] ?? ''), '/');
    $targetPath = $link !== '' ? realpath($root . '/' . $link) : false;
    $uploadDirReal = realpath($uploadDir);
    $isInsideUploadDir = $targetPath && $uploadDirReal && strpos($targetPath, $uploadDirReal) === 0;

    if ($link !== '' && strpos($link, 'uploads/board/') === 0 && $isInsideUploadDir && file_exists($targetPath)) {
        $response = ['success' => (bool) $store->deletePath($targetPath)];
    } else {
        $response = ['success' => false];
    }
}

echo json_encode($response);
