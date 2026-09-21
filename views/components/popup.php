<?php

use Database\DB;

$db = DB::getInstance();

$rawPath     = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$basePath = defined('BASE_PATH') ? trim((string) BASE_PATH, '/') : '';
$normalizedRawPath = '/' . trim($rawPath, '/');
if ($normalizedRawPath === '/') {
    $currentPath = '/';
} elseif ($basePath !== '' && strpos($normalizedRawPath, '/' . $basePath . '/') === 0) {
    $currentPath = substr($normalizedRawPath, strlen('/' . $basePath));
} elseif ($basePath !== '' && $normalizedRawPath === '/' . $basePath) {
    $currentPath = '/';
} else {
    $currentPath = $normalizedRawPath;
}

$currentPath = '/' . trim($currentPath, '/');
if ($currentPath === '') $currentPath = '/';
if ($currentPath !== '/') {
    return;
}

$stmt = $db->prepare("
    SELECT *
    FROM nb_popups
    WHERE is_active = 1
      AND popup_path = '/'
      AND (
            is_unlimited = 1
            OR (
                is_unlimited = 0
                AND (start_at IS NULL OR start_at <= NOW())
                AND (end_at   IS NULL OR end_at   >= NOW())
            )
          )
    ORDER BY sort_no ASC, id ASC
");
$stmt->execute();
$popups = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($popups)) return;

include __DIR__ . '/image-popup.php';
