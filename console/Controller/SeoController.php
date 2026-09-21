<?php

require_once "../bootstrap/admin.php";
require_once "../Model/SeoModel.php";
require_once "../core/Validator.php";
$role->requireLogin();
$role->requireCanModify();

header('Content-Type: application/json');

$LOCALES = function_exists('project_supported_locales') ? project_supported_locales() : ['en', 'ko', 'jp'];
$DEFAULT_LOCALE = function_exists('project_default_locale') ? project_default_locale() : 'en';

/**
 * 폼 입력에서 locale 별 컬럼 값을 추출
 */
$collectLocaleData = static function (array $input) use ($LOCALES): array {
    $data = ['path' => trim((string)($input['path'] ?? ''))];
    foreach ($LOCALES as $lc) {
        foreach (SeoModel::LOCALE_FIELDS as $field) {
            $col = "{$field}_{$lc}";
            $data[$col] = isset($input[$col]) ? (string)$input[$col] : '';
        }
    }
    // 페이지별 robots 메타 (공란이면 사이트 전역 meta_robots로 fallback)
    $data['robots'] = isset($input['robots']) ? (string)$input['robots'] : '';
    // 비-locale 단일 필드
    $data['og_image']     = isset($input['og_image'])     ? (string)$input['og_image']     : '';
    $data['og_type']      = isset($input['og_type'])      ? (string)$input['og_type']      : '';
    $data['twitter_card'] = isset($input['twitter_card']) ? (string)$input['twitter_card'] : '';
    return $data;
};

/**
 * og_image 파일 업로드 처리: 새 파일이 있으면 업로드, 없으면 기존 값 유지
 */
$resolveOgImage = static function (array &$input): void {
    global $UPLOAD_META_DIR, $UPLOAD_META_WDIR;
    $file = $_FILES['og_image_file'] ?? null;
    if ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $oldFilename = basename($input['og_image_existing'] ?? '');
        $up = imageUpload($UPLOAD_META_DIR, $file, $oldFilename, false);
        $saved = $up['saved'] ?? '';
        $input['og_image'] = $saved !== '' ? ($UPLOAD_META_WDIR . '/' . $saved) : ($input['og_image_existing'] ?? '');
    } else {
        $input['og_image'] = $input['og_image_existing'] ?? '';
    }
};

try {
    $input = $_POST;
    $mode = $input['mode'] ?? '';

    // ============ INSERT ============
    if ($mode === 'insert') {
        $validator = new Validator();
        $validator->require('path', $input['path'] ?? '', '경로');
        // 기본 언어의 페이지 제목만 필수 (그 외는 fallback)
        $validator->require(
            "page_title_{$DEFAULT_LOCALE}",
            $input["page_title_{$DEFAULT_LOCALE}"] ?? '',
            '페이지 제목 (' . strtoupper($DEFAULT_LOCALE) . ')'
        );

        if ($validator->fails()) {
            echo json_encode([
                'success' => false,
                'message' => implode("\n", $validator->getErrors())
            ]);
            exit;
        }

        $resolveOgImage($input);
        $result = SeoModel::insert($collectLocaleData($input));

        echo json_encode([
            'success' => $result,
            'message' => $result ? '등록되었습니다.' : '등록 실패'
        ]);
        exit;
    }

    // ============ UPDATE ============
    if ($mode === 'update') {
        $id = (int)($input['id'] ?? 0);

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID가 없습니다.']);
            exit;
        }

        $validator = new Validator();
        $validator->require('path', $input['path'] ?? '', '경로');
        $validator->require(
            "page_title_{$DEFAULT_LOCALE}",
            $input["page_title_{$DEFAULT_LOCALE}"] ?? '',
            '페이지 제목 (' . strtoupper($DEFAULT_LOCALE) . ')'
        );

        if ($validator->fails()) {
            echo json_encode([
                'success' => false,
                'message' => implode("\n", $validator->getErrors())
            ]);
            exit;
        }

        $resolveOgImage($input);
        $result = SeoModel::update($id, $collectLocaleData($input));

        echo json_encode([
            'success' => $result,
            'message' => $result ? '수정되었습니다.' : '수정 실패'
        ]);
        exit;
    }

    // ============ DELETE ============
    if ($mode === 'delete') {
        $id = (int)($input['id'] ?? 0);

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID가 없습니다.']);
            exit;
        }

        $result = SeoModel::delete($id);

        echo json_encode([
            'success' => $result,
            'message' => $result ? '삭제되었습니다.' : '삭제 실패'
        ]);
        exit;
    }

    // ============ DELETE_ARRAY ============
    if ($mode === 'delete_array') {
        $ids = json_decode($input['ids'] ?? '[]', true);

        if (!is_array($ids) || count($ids) === 0) {
            echo json_encode(['success' => false, 'message' => '삭제할 항목이 없습니다.']);
            exit;
        }

        $result = SeoModel::deleteMultiple($ids);

        echo json_encode([
            'success' => $result,
            'message' => $result ? '선택 항목이 삭제되었습니다.' : '삭제 실패'
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => '유효하지 않은 요청입니다.']);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '처리 중 오류 발생: ' . $e->getMessage()
    ]);
}
