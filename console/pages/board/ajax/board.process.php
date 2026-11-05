<?php
include_once "../../../bootstrap/admin.php";
include_once "../../../lib/admin.check.ajax.php";
require_once dirname(__DIR__, 3) . '/Model/BoardLocaleModel.php';
$role->requireLogin();
$role->requireCanModify();
header('Content-Type: application/json; charset=utf-8');

$mode = (string)($_POST['mode'] ?? '');
$DEFAULT_LOCALE = function_exists('project_default_locale') ? project_default_locale() : 'en';
$SUPPORTED_LOCALES = function_exists('project_supported_locales') ? project_supported_locales() : ['en', 'ko', 'jp'];
$db = DB::getInstance();
$newFiles = [];
$oldFiles = [];
$committed = false;
$fileFields = ['thumb_image'];
for ($i = 1; $i <= 5; $i++) $fileFields[] = "file_attach_{$i}";

$respond = static function (bool $ok, string $message): void {
    echo json_encode(['result' => $ok ? 'success' : 'fail', 'msg' => $message], JSON_UNESCAPED_UNICODE);
};
$requireTransactions = static function () use ($db): void {
    static $checked = false;
    if ($checked) return;
    $rows = $db->query("SHOW TABLE STATUS WHERE Name IN ('nb_board', 'nb_board_manage', 'nb_board_locale', 'nb_board_comment')")->fetchAll(PDO::FETCH_ASSOC);
    if (count($rows) !== 4) throw new RuntimeException('게시판 테이블 설정을 확인해 주세요.');
    foreach ($rows as $row) {
        if (strcasecmp((string)$row['Engine'], 'InnoDB') !== 0) throw new RuntimeException('게시판 저장 전에 InnoDB 데이터베이스 업데이트를 적용해 주세요.');
    }
    $checked = true;
};
$positiveId = static function ($value): int {
    return is_scalar($value) && preg_match('/^[1-9][0-9]*$/D', (string)$value) ? (int)$value : 0;
};
$findBoard = static function (int $boardNo, bool $lock = false) use ($db, $NO_SITE_UNIQUE_KEY): array {
    $stmt = $db->prepare('SELECT * FROM nb_board_manage WHERE no = ? AND sitekey = ?' . ($lock ? ' FOR UPDATE' : ''));
    $stmt->execute([$boardNo, $NO_SITE_UNIQUE_KEY]);
    $board = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$board) throw new RuntimeException('게시판을 찾을 수 없습니다.');
    return $board;
};
$findPost = static function (int $id, int $boardNo = 0, bool $lock = false) use ($db, $NO_SITE_UNIQUE_KEY): array {
    $stmt = $db->prepare('SELECT * FROM nb_board WHERE no = ? AND sitekey = ?' . ($boardNo ? ' AND board_no = ?' : '') . ($lock ? ' FOR UPDATE' : ''));
    $stmt->execute($boardNo ? [$id, $NO_SITE_UNIQUE_KEY, $boardNo] : [$id, $NO_SITE_UNIQUE_KEY]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) throw new RuntimeException('게시물을 찾을 수 없습니다.');
    return $row;
};
$writeRow = static function (array $data, ?int $id = null, ?int $sourceBoard = null) use ($db, $NO_SITE_UNIQUE_KEY): void {
    if ($id === null) {
        $keys = array_keys($data);
        $stmt = $db->prepare('INSERT INTO nb_board (`' . implode('`,`', $keys) . '`) VALUES (' . implode(',', array_fill(0, count($keys), '?')) . ')');
        if (!$stmt->execute(array_values($data))) throw new RuntimeException('등록에 실패했습니다.');
    } else {
        $scope = $sourceBoard ?? $data['board_no'];
        $set = implode(',', array_map(static fn($key) => "`{$key}` = ?", array_keys($data)));
        $stmt = $db->prepare('UPDATE nb_board SET ' . $set . ' WHERE no = ? AND sitekey = ? AND board_no = ?');
        if (!$stmt->execute([...array_values($data), $id, $NO_SITE_UNIQUE_KEY, $scope])) throw new RuntimeException('수정에 실패했습니다.');
    }
};
// Upload helpers throw instead of exiting, so every already-created file can be rolled back.
$upload = static function (array $file) use (&$newFiles, $UPLOAD_DIR_BOARD): array {
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $name = bin2hex(random_bytes(16)) . '.' . $ext;
    if (!is_dir($UPLOAD_DIR_BOARD) && !mkdir($UPLOAD_DIR_BOARD, 0755, true) && !is_dir($UPLOAD_DIR_BOARD)) throw new RuntimeException('업로드 경로를 생성하지 못했습니다.');
    if (!move_uploaded_file($file['tmp_name'], $UPLOAD_DIR_BOARD . '/' . $name)) throw new RuntimeException('파일 업로드에 실패했습니다.');
    $newFiles[] = $name;
    if (in_array($ext, ['jpg', 'jpeg', 'png'], true) && class_exists('ImageHelper')) ImageHelper::generateWebpVariant($UPLOAD_DIR_BOARD . '/' . $name);
    return ['saved' => $name, 'origin' => $file['name']];
};
$copyFile = static function (string $name) use (&$newFiles, $UPLOAD_DIR_BOARD): string {
    if ($name !== basename($name) || !is_file($UPLOAD_DIR_BOARD . '/' . $name)) throw new RuntimeException('복사할 파일을 찾을 수 없습니다.');
    $copy = bin2hex(random_bytes(16)) . '.' . pathinfo($name, PATHINFO_EXTENSION);
    if (!copy($UPLOAD_DIR_BOARD . '/' . $name, $UPLOAD_DIR_BOARD . '/' . $copy)) throw new RuntimeException('파일 복사에 실패했습니다.');
    $newFiles[] = $copy;
    if (class_exists('ImageHelper') && in_array(strtolower(pathinfo($copy, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png'], true)) ImageHelper::generateWebpVariant($UPLOAD_DIR_BOARD . '/' . $copy);
    return $copy;
};
// Inline editor images are shared on copy. Never remove a file still referenced by any post/locale.
$removeUnreferenced = static function (string $name) use ($db, $fileFields, $UPLOAD_DIR_BOARD): void {
    if ($name === '' || basename($name) !== $name) return;
    $conditions = array_map(static fn($field) => "`{$field}` = ?", $fileFields);
    $stmt = $db->prepare('SELECT 1 FROM nb_board WHERE ' . implode(' OR ', $conditions) . ' OR contents LIKE ? LIMIT 1');
    $stmt->execute([...array_fill(0, count($fileFields), $name), '%' . $name . '%']);
    if ($stmt->fetchColumn()) return;
    $stmt = $db->prepare('SELECT 1 FROM nb_board_locale WHERE contents LIKE ? LIMIT 1');
    $stmt->execute(['%' . $name . '%']);
    if (!$stmt->fetchColumn()) imageDelete($UPLOAD_DIR_BOARD . '/' . $name);
};

try {
    if (in_array($mode, ['save', 'edit', 'delete', 'delete.array', 'board.copy'], true)) {
        $requireTransactions();
        $boardNo = $positiveId($_POST['board_no'] ?? 0);
        $id = $positiveId($_POST['no'] ?? 0);
        $targets = [];
        if (in_array($mode, ['save', 'edit'], true)) {
            if (!$boardNo) throw new RuntimeException('게시판을 선택해 주세요.');
            $findBoard($boardNo);
        }
        if (in_array($mode, ['edit', 'delete', 'board.copy'], true)) {
            if (!$id) throw new RuntimeException('잘못된 게시물 번호입니다.');
            $targets[] = $findPost($id, $mode === 'edit' ? 0 : $boardNo);
            if ($mode !== 'edit') $boardNo = (int)$targets[0]['board_no'];
        } elseif ($mode === 'delete.array') {
            $ids = $_POST['board_file_check_no'] ?? [];
            if (!is_array($ids) || !$ids) throw new RuntimeException('삭제할 항목이 없습니다.');
            foreach (array_unique($ids) as $rawId) {
                $targetId = $positiveId($rawId);
                if (!$targetId) throw new RuntimeException('잘못된 게시물 번호입니다.');
                $targets[] = $findPost($targetId, $boardNo);
            }
        }
        $payload = [];
        $localePayloads = [];
        $validatedUploads = [];
        if (in_array($mode, ['save', 'edit'], true)) {
            $existingLocales = $mode === 'edit' ? BoardLocaleModel::findAllByBoardId($id) : [];
            foreach ($SUPPORTED_LOCALES as $lc) {
                $existingLocale = $existingLocales[$lc] ?? ($lc === $DEFAULT_LOCALE ? ($targets[0] ?? []) : []);
                $localeTitle = $_POST["title_{$lc}"] ?? ($existingLocale['title'] ?? '');
                if (!is_string($localeTitle)) throw new RuntimeException('제목을 확인해 주세요.');
                $raw = $_POST["contents_{$lc}"] ?? ($existingLocale['contents'] ?? '');
                if (!is_string($raw)) throw new RuntimeException('본문을 확인해 주세요.');
                if (($_POST["contents_encoding_{$lc}"] ?? '') === 'base64') {
                    $raw = base64_decode($raw, true);
                    if ($raw === false) throw new RuntimeException('본문 형식을 확인해 주세요.');
                }
                $localePayloads[$lc] = ['title' => trim($localeTitle), 'contents' => $raw, 'write_name' => (string)($_POST['write_name'] ?? ($targets[0]['write_name'] ?? ''))];
                for ($i = 1; $i <= 30; $i++) $localePayloads[$lc]["extra{$i}"] = (string)($_POST["extra{$i}"] ?? ($existingLocale["extra{$i}"] ?? $targets[0]["extra{$i}"] ?? ''));
            }
            if (trim(strip_tags($localePayloads[$DEFAULT_LOCALE]['title'] ?? '')) === '') throw new RuntimeException('제목을 입력해 주세요.');
            if ((TextHelper::utf8Length($localePayloads[$DEFAULT_LOCALE]['title']) ?? 201) > 200) throw new RuntimeException('제목은 200자 이하로 입력해 주세요.');
            $base = $localePayloads[$DEFAULT_LOCALE];
            $payload = ['board_no' => $boardNo, 'title' => $base['title'], 'contents' => $base['contents'], 'write_name' => $base['write_name'], 'category_no' => (int)($_POST['category_no'] ?? 0), 'is_notice' => ($_POST['is_notice'] ?? 'N') === 'Y' ? 'Y' : 'N', 'direct_url' => (string)($_POST['direct_url'] ?? ''), 'regdate' => ($_POST['regdate'] ?? '') ?: ($targets[0]['regdate'] ?? date('Y-m-d H:i:s'))];
            for ($i = 1; $i <= 30; $i++) $payload["extra{$i}"] = $base["extra{$i}"];
            if ($payload['category_no'] > 0) {
                $stmt = $db->prepare('SELECT 1 FROM nb_board_category WHERE no = ? AND sitekey = ? AND board_no = ?');
                $stmt->execute([$payload['category_no'], $NO_SITE_UNIQUE_KEY, $boardNo]);
                if (!$stmt->fetchColumn()) throw new RuntimeException('분류를 확인해 주세요.');
            }
            foreach (['thumb_image', 'addFile1', 'addFile2', 'addFile3', 'addFile4', 'addFile5'] as $field) {
                $file = $_FILES[$field] ?? [];
                $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
                if ($error === UPLOAD_ERR_NO_FILE) continue;
                if ($error !== UPLOAD_ERR_OK || !is_string($file['name'] ?? null) || !is_string($file['tmp_name'] ?? null) || !is_uploaded_file($file['tmp_name'])) throw new RuntimeException('파일 업로드 상태를 확인해 주세요.');
                if ((int)$file['size'] > 10485760) throw new RuntimeException('파일은 10MB 이하로 등록해 주세요.');
                if (!in_array(strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)), $board_file_allow, true)) throw new RuntimeException('허용되지 않는 파일입니다.');
                $validatedUploads[$field] = $file;
            }
        }
        $db->beginTransaction();
        $boardIds = $mode === 'save' ? [$boardNo] : array_values(array_unique(array_map(static fn($row) => (int)$row['board_no'], $targets)));
        if ($mode === 'edit') $boardIds = array_values(array_unique([...$boardIds, $boardNo]));
        sort($boardIds);
        foreach ($boardIds as $lockBoard) $findBoard($lockBoard, true);
        foreach ($targets as &$target) $target = $findPost((int)$target['no'], (int)$target['board_no'], true);
        unset($target);
        if ($mode === 'save' || $mode === 'board.copy') {
            $stmt = $db->prepare('UPDATE nb_board SET sort_no = sort_no + 1 WHERE sitekey = ? AND board_no = ?');
            $stmt->execute([$NO_SITE_UNIQUE_KEY, $boardNo]);
        }
        if ($mode === 'edit' && (int)$targets[0]['board_no'] !== $boardNo) {
            $stmt = $db->prepare('UPDATE nb_board SET sort_no = sort_no - 1 WHERE sitekey = ? AND board_no = ? AND sort_no > ?');
            $stmt->execute([$NO_SITE_UNIQUE_KEY, $targets[0]['board_no'], $targets[0]['sort_no']]);
            $stmt = $db->prepare('UPDATE nb_board SET sort_no = sort_no + 1 WHERE sitekey = ? AND board_no = ?');
            $stmt->execute([$NO_SITE_UNIQUE_KEY, $boardNo]);
            $payload['sort_no'] = 1;
        }
        if ($mode === 'save' || $mode === 'edit') {
            if ($mode === 'save') $payload += ['sitekey' => $NO_SITE_UNIQUE_KEY, 'user_no' => -1, 'sort_no' => 1];
            $remove = $_POST['attach_file_del'] ?? [];
            if (!is_array($remove)) throw new RuntimeException('파일 삭제 항목을 확인해 주세요.');
            if ($mode === 'edit') foreach ($remove as $slot) {
                if (!in_array((string)$slot, ['0','1','2','3','4','5'], true)) throw new RuntimeException('파일 삭제 항목을 확인해 주세요.');
                $field = (string)$slot === '0' ? 'thumb_image' : 'file_attach_' . $slot;
                $oldFiles[] = (string)($targets[0][$field] ?? '');
                $payload[$field] = null;
                if ($field !== 'thumb_image') $payload['file_attach_origin_' . $slot] = null;
            }
            foreach ($validatedUploads as $input => $file) {
                $uploaded = $upload($file);
                $field = $input === 'thumb_image' ? 'thumb_image' : 'file_attach_' . substr($input, 7);
                $payload[$field] = $uploaded['saved'];
                if ($input !== 'thumb_image') $payload['file_attach_origin_' . substr($input, 7)] = $uploaded['origin'];
                if ($mode === 'edit') $oldFiles[] = (string)($targets[0][$field] ?? '');
            }
            $writeRow($payload, $mode === 'edit' ? $id : null, $mode === 'edit' ? (int)$targets[0]['board_no'] : null);
            $savedId = $mode === 'edit' ? $id : (int)$db->lastInsertId();
            foreach ($localePayloads as $lc => $localeData) if (!BoardLocaleModel::upsert($savedId, $lc, $localeData)) throw new RuntimeException('언어별 저장에 실패했습니다.');
        } elseif ($mode === 'board.copy') {
            $copy = $targets[0];
            unset($copy['no']);
            $copy['sort_no'] = 1;
            $copy['comment_cnt'] = 0;
            $copy['read_cnt'] = 0;
            foreach ($fileFields as $field) if (!empty($copy[$field])) $copy[$field] = $copyFile($copy[$field]);
            // Preserve the exact editor HTML and locale bodies without requiring DOM.
            $writeRow($copy);
            BoardLocaleModel::copyForBoard($id, (int)$db->lastInsertId());
        } else {
            foreach ($targets as $row) {
                $deleteId = (int)$row['no'];
                foreach ($fileFields as $field) $oldFiles[] = (string)($row[$field] ?? '');
                $stmt = $db->prepare('DELETE FROM nb_board_comment WHERE parent_no = ? AND sitekey = ?');
                $stmt->execute([$deleteId, $NO_SITE_UNIQUE_KEY]);
                if (!BoardLocaleModel::deleteByBoardId($deleteId)) throw new RuntimeException('언어별 삭제에 실패했습니다.');
                $stmt = $db->prepare('DELETE FROM nb_board WHERE no = ? AND sitekey = ? AND board_no = ?');
                $stmt->execute([$deleteId, $NO_SITE_UNIQUE_KEY, $row['board_no']]);
            }
            foreach ($boardIds as $scope) {
                $stmt = $db->prepare('SELECT no FROM nb_board WHERE sitekey = ? AND board_no = ? ORDER BY sort_no ASC, no DESC');
                $stmt->execute([$NO_SITE_UNIQUE_KEY, $scope]);
                foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $index => $remainingId) {
                    $update = $db->prepare('UPDATE nb_board SET sort_no = ? WHERE no = ? AND sitekey = ? AND board_no = ?');
                    $update->execute([$index + 1, $remainingId, $NO_SITE_UNIQUE_KEY, $scope]);
                }
            }
        }
        if (!$db->commit()) throw new RuntimeException('데이터 저장을 완료하지 못했습니다.');
        $committed = true;
        foreach (array_unique($oldFiles) as $name) $removeUnreferenced($name);
        $messages = ['save' => '정상적으로 등록되었습니다.', 'edit' => '정상적으로 수정되었습니다.', 'board.copy' => '정상적으로 복사 되었습니다.', 'delete' => '정상적으로 삭제되었습니다.', 'delete.array' => '정상적으로 삭제되었습니다.'];
        $respond(true, $messages[$mode]);
        exit;
    }
    if (in_array($mode, ['board.category', 'category.depth', 'board.field'], true)) $findBoard($positiveId($_REQUEST['board_no'] ?? 0));

if ($mode == "board.category") {
    $board_no = $_REQUEST['board_no'];
    $boardManage_info = getBoardManageInfoByNo($board_no);
    $category_yn = $boardManage_info[0]['category_yn'];

    try {
        $query = "SELECT no, name FROM nb_board_category WHERE sitekey = :sitekey AND board_no = :board_no ORDER BY sort_no ASC";
        $stmt = DB::getInstance()->prepare($query);
        $stmt->execute([':sitekey' => $NO_SITE_UNIQUE_KEY, ':board_no' => $board_no]);

        $rows = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = ["no" => $row['no'], "name" => $row['name']];
        }

        echo json_encode([
            "result" => "success",
            "category_yn" => $category_yn,
            "rows" => $rows,
            "msg" => "정상적으로 불러왔습니다."
        ]);
    } catch (PDOException $e) {
        echo json_encode(["result" => "fail", "msg" => "DB 처리중 오류가 발생하였습니다.[Error: " . $e->getMessage() . "]"]);
    }
} else if ($mode == "category.depth") {
    $board_no = $_REQUEST['board_no'];
    $boardManage_info = getBoardManageInfoByNo($board_no);
    $depth_category_yn = $boardManage_info[0]['depth_category_yn'];

    echo json_encode([
        "result" => "success",
        "depth_category_yn" => $depth_category_yn,
        "msg" => "정상적으로 불러왔습니다."
    ]);
} else if ($mode == "board.field") {
	 $board_no = $_REQUEST['board_no'];
		$boardManage_info = getBoardManageInfoByNo($board_no);

		$rows = [];

		foreach ($boardManage_info[0] as $k => $v) {
			if (preg_match('/^extra_match_field(\d{1,2})$/', $k, $matches) && !empty($v)) {
				$i = $matches[1]; 
				$rows[] = ["name" => $v, "fields" => "extra{$i}"];
			}
		}

		echo json_encode([
			'test' => true,
			'result' => 'success',
			'rows' => $rows,
			'msg' => '정상적으로 불러왔습니다.',
		]);
}

} catch (Throwable $e) {
    if (!$committed) {
        try {
            if ($db->inTransaction()) $db->rollBack();
        } catch (Throwable $rollbackError) {
            error_log('[board-process] rollback failed');
        }
        foreach ($newFiles as $name) imageDelete($UPLOAD_DIR_BOARD . '/' . $name);
    }
    $respond(false, $e instanceof PDOException ? '데이터 저장 중 오류가 발생했습니다.' : $e->getMessage());
}
