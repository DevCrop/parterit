<?php
declare(strict_types=1);

include_once __DIR__ . '/base.class.php';
require_once __DIR__ . '/core/DownloadException.php';
require_once __DIR__ . '/core/BoardFileDownloadService.php';

try {
    $store   = new FileStore((string) ($UPLOAD_DIR_BOARD ?? ''));
    $service = new BoardFileDownloadService(DB::getInstance(), $store);

    $service->download(
        isset($_GET['no'])  ? (int)    $_GET['no']  : 0,
        isset($_GET['fld']) ? (string) $_GET['fld'] : ''
    );
} catch (DownloadException $e) {
    http_response_code($e->httpStatus());
    exit($e->getMessage());
} catch (Throwable $e) {
    // DB/스토리지 등 예기치 못한 오류 — 상세는 로그로만 남기고 응답은 일반 메시지
    error_log('[board.file.download] ' . get_class($e) . ': ' . $e->getMessage());
    if (!headers_sent()) {
        http_response_code(500);
    }
    exit('파일 다운로드 처리 중 오류가 발생했습니다. 관리자에게 문의해주세요.');
}

exit;
