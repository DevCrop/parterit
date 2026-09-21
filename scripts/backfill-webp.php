<?php

/**
 * 기존 jpg/png 자산에 .webp 파생본을 일괄 생성한다.
 *
 * 적용 범위:
 *   - uploads/    (관리자 업로드 자산: board / banners / popups / logo / meta / employment …)
 *   - resource/images/  (정적 자산: 페이지 figure, 로고, 히어로 등)
 *
 * 사용법 (Docker):
 *   docker compose exec web php scripts/backfill-webp.php
 *   docker compose exec web php scripts/backfill-webp.php --dry-run
 *   docker compose exec web php scripts/backfill-webp.php --target=uploads
 *   docker compose exec web php scripts/backfill-webp.php --target=resource
 *
 * - JPG/JPEG/PNG 만 변환 (대소문자 무관)
 * - 이미 .webp 짝꿍이 있으면 스킵 (재실행 안전)
 * - 신규 업로드는 imageUpload() 가 자동 변환하므로, 본 스크립트는 1회성 백필 용도
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from the command line.\n");
    exit(1);
}

// 큰 PNG/JPG 디코드는 raw RGB로 들어가 메모리 폭증 — 백필 동안만 한도 상향.
ini_set('memory_limit', '1024M');

require __DIR__ . '/../inc/lib/base.class.php';

// 인자 파싱
$dryRun = in_array('--dry-run', $argv, true);
$target = 'all';
foreach ($argv as $arg) {
    if (strpos($arg, '--target=') === 0) {
        $target = substr($arg, strlen('--target='));
    }
}

$projectRoot = realpath(__DIR__ . '/..');
if ($projectRoot === false) {
    fwrite(STDERR, "Cannot resolve project root.\n");
    exit(1);
}

$roots = [];
if ($target === 'uploads' || $target === 'all') {
    $uploads = $UPLOAD_DIR_BASE ?? ($projectRoot . '/uploads');
    if (is_dir($uploads)) $roots[] = $uploads;
}
if ($target === 'resource' || $target === 'all') {
    $resourceImages = $projectRoot . '/resource/images';
    if (is_dir($resourceImages)) $roots[] = $resourceImages;
}

if (empty($roots)) {
    fwrite(STDERR, "No target directories resolved (target={$target}).\n");
    exit(1);
}

echo "Mode:    " . ($dryRun ? 'DRY-RUN' : 'EXECUTE') . PHP_EOL;
echo "Target:  {$target}" . PHP_EOL;
echo "Roots:   " . implode(', ', $roots) . PHP_EOL;
echo str_repeat('-', 60) . PHP_EOL;

$ok = 0; $skip = 0; $fail = 0; $scanned = 0;

foreach ($roots as $root) {
    $iter = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iter as $fileInfo) {
        if (!$fileInfo->isFile()) continue;
        $scanned++;

        $path = $fileInfo->getPathname();
        $ext  = strtolower($fileInfo->getExtension());
        if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) continue;

        $base = $fileInfo->getPath() . DIRECTORY_SEPARATOR . pathinfo($path, PATHINFO_FILENAME);
        $webp = $base . '.webp';

        if (is_file($webp)) {
            $skip++;
            continue;
        }

        if ($dryRun) {
            echo "WOULD  {$path}" . PHP_EOL;
            continue;
        }

        $result = ImageHelper::generateWebpVariant($path);
        if ($result !== null) {
            $ok++;
            echo "OK     {$path}" . PHP_EOL;
        } else {
            $fail++;
            echo "FAIL   {$path}" . PHP_EOL;
        }
    }
}

echo str_repeat('-', 60) . PHP_EOL;
echo "Scanned: {$scanned}  |  Converted: {$ok}  |  Skipped (already webp): {$skip}  |  Failed: {$fail}" . PHP_EOL;
echo $dryRun ? "(dry-run — no files written)" . PHP_EOL : "Done." . PHP_EOL;
