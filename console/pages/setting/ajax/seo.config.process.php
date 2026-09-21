<?php

include_once "../../../bootstrap/admin.php";
include_once "../../../lib/admin.check.ajax.php";

$pdo = DB::getInstance();
$role->requireLogin();
$role->requireCanModify();

header('Content-Type: application/json; charset=UTF-8');

$mode = $_POST['mode'] ?? $_GET['mode'] ?? '';

$LOCALES = function_exists('project_supported_locales') ? project_supported_locales() : ['en', 'ko', 'jp'];
$DEFAULT_LOCALE = function_exists('project_default_locale') ? project_default_locale() : 'en';

$sitemapAbsPath = ROOT_PATH . '/sitemap.xml';

if ($mode === 'seo.config.save') {
    // 다국어 메타 컬럼 수집
    $localeFields = ['title', 'meta_title', 'meta_description', 'meta_keywords'];
    $params = [':sitekey' => $NO_SITE_UNIQUE_KEY];
    $sets = [];

    foreach ($LOCALES as $lc) {
        foreach ($localeFields as $field) {
            $col = "{$field}_{$lc}";
            $val = isset($_POST[$col]) ? trim((string)$_POST[$col]) : '';
            $sets[] = "`{$col}` = :{$col}";
            $params[":{$col}"] = $val !== '' ? $val : null;
        }
    }

    // legacy 컬럼 미러링: 기본 언어 값으로 채워서 SEO Manager fallback 호환 유지
    foreach ($localeFields as $field) {
        $defaultVal = $_POST["{$field}_{$DEFAULT_LOCALE}"] ?? '';
        $defaultVal = trim((string)$defaultVal);
        $sets[] = "`{$field}` = :legacy_{$field}";
        $params[":legacy_{$field}"] = $defaultVal !== '' ? $defaultVal : null;
    }

    // 파일 업로드 (favicon / og 썸네일)
    $oldFavicon = $_POST['meta_favicon_ico_filename'] ?? '';
    $oldThumb = $_POST['meta_thumb_filename'] ?? '';
    $newFavicon = '';
    $newThumb = '';

    if (isset($_FILES['meta_favicon_ico']) && ($_FILES['meta_favicon_ico']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $up = imageUpload($UPLOAD_META_DIR, $_FILES['meta_favicon_ico'], $oldFavicon, false);
        $newFavicon = $up['saved'] ?? '';
    }
    if (isset($_FILES['meta_thumb']) && ($_FILES['meta_thumb']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $up = imageUpload($UPLOAD_META_DIR, $_FILES['meta_thumb'], $oldThumb, false);
        $newThumb = $up['saved'] ?? '';
    }

    if ($newFavicon !== '') {
        $sets[] = "`meta_favicon_ico` = :meta_favicon_ico";
        $params[':meta_favicon_ico'] = $newFavicon;
    }
    if ($newThumb !== '') {
        $sets[] = "`meta_thumb` = :meta_thumb";
        $params[':meta_thumb'] = $newThumb;
    }

    // 행 존재 여부 → UPDATE / INSERT
    $check = $pdo->prepare("SELECT no FROM nb_siteinfo WHERE sitekey = :sitekey LIMIT 1");
    $check->execute([':sitekey' => $NO_SITE_UNIQUE_KEY]);
    $exists = $check->fetchColumn();

    try {
        if ($exists) {
            $sql = "UPDATE nb_siteinfo SET " . implode(', ', $sets) . " WHERE sitekey = :sitekey";
            $stmt = $pdo->prepare($sql);
            $ok = $stmt->execute($params);
        } else {
            // INSERT — 컬럼/플레이스홀더 분리
            $cols = ['sitekey'];
            $placeholders = [':sitekey'];
            foreach ($params as $k => $_) {
                if ($k === ':sitekey') continue;
                $colName = ltrim(str_replace('legacy_', '', $k), ':');
                $cols[] = "`{$colName}`";
                $placeholders[] = $k;
            }
            $sql = "INSERT INTO nb_siteinfo (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $pdo->prepare($sql);
            $ok = $stmt->execute($params);
        }
        echo json_encode([
            'result' => $ok ? 'success' : 'fail',
            'msg' => $ok ? '정상적으로 저장되었습니다.' : '처리 중 문제가 발생했습니다.',
        ]);
    } catch (Throwable $e) {
        echo json_encode(['result' => 'fail', 'msg' => 'DB 오류: ' . $e->getMessage()]);
    }
    exit;
}

if ($mode === 'sitemap.generate') {
    try {
        // routes/web.php 가 등록되지 않은 상태이면 include
        $routesFile = ROOT_PATH . '/routes/web.php';
        if (is_file($routesFile)) {
            $router = app()->router();
            if (empty($router->getNamedRoutes())) {
                include_once $routesFile;
            }
        }

        $xml = sitemap_generator()->generateXml();
        $bytes = file_put_contents($sitemapAbsPath, $xml);

        if ($bytes === false) {
            echo json_encode(['result' => 'fail', 'msg' => 'sitemap.xml 파일을 쓸 수 없습니다. 권한을 확인해주세요.']);
            exit;
        }

        $count = substr_count($xml, '<url>');
        echo json_encode([
            'result' => 'success',
            'msg' => "사이트맵을 생성했습니다. ({$count}개 URL, " . number_format($bytes) . " bytes)",
            'count' => $count,
            'bytes' => $bytes,
        ]);
    } catch (Throwable $e) {
        echo json_encode(['result' => 'fail', 'msg' => '생성 실패: ' . $e->getMessage()]);
    }
    exit;
}

if ($mode === 'sitemap.upload') {
    if (!isset($_FILES['sitemap_xml']) || ($_FILES['sitemap_xml']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        echo json_encode(['result' => 'fail', 'msg' => '업로드된 파일이 없습니다.']);
        exit;
    }

    $file = $_FILES['sitemap_xml'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['result' => 'fail', 'msg' => '업로드 오류 (코드: ' . $file['error'] . ')']);
        exit;
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode(['result' => 'fail', 'msg' => '5MB 이하 파일만 업로드 가능합니다.']);
        exit;
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'xml') {
        echo json_encode(['result' => 'fail', 'msg' => 'XML 파일만 업로드 가능합니다.']);
        exit;
    }

    $contents = file_get_contents($file['tmp_name']);
    if ($contents === false || $contents === '') {
        echo json_encode(['result' => 'fail', 'msg' => '파일을 읽을 수 없습니다.']);
        exit;
    }

    libxml_use_internal_errors(true);
    $sx = simplexml_load_string($contents);
    if ($sx === false) {
        $errs = array_map(static fn($e) => trim($e->message), libxml_get_errors());
        libxml_clear_errors();
        echo json_encode(['result' => 'fail', 'msg' => 'XML 파싱 실패: ' . implode(' / ', array_slice($errs, 0, 3))]);
        exit;
    }

    if (!move_uploaded_file($file['tmp_name'], $sitemapAbsPath)) {
        echo json_encode(['result' => 'fail', 'msg' => '파일 저장 실패. 권한을 확인해주세요.']);
        exit;
    }

    echo json_encode([
        'result' => 'success',
        'msg' => '사이트맵을 업로드했습니다. (' . number_format(filesize($sitemapAbsPath)) . ' bytes)',
    ]);
    exit;
}

echo json_encode(['result' => 'fail', 'msg' => '유효하지 않은 요청입니다.']);
