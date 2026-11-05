<?php

include_once "../../../bootstrap/admin.php";
include_once "../../../lib/admin.check.ajax.php";

$pdo = DB::getInstance();
$mode = $_POST['mode'];

$isValidPasswordPolicy = static function ($value): bool {
    $value = (string)$value;
    if (strlen($value) < 8) {
        return false;
    }

    $groups = 0;
    $groups += preg_match('/[A-Z]/', $value) ? 1 : 0;
    $groups += preg_match('/[a-z]/', $value) ? 1 : 0;
    $groups += preg_match('/\d/', $value) ? 1 : 0;
    $groups += preg_match('/[^A-Za-z0-9]/', $value) ? 1 : 0;

    return $groups >= 3;
};

if ($mode == "pwd.change") {

    $pwd_old = $_POST['pwd_old'];
    $pwd_new = $_POST['pwd_new'];
    $pwd_new_confirm = $_POST['pwd_new_confirm'];

    // 세션에서 현재 로그인한 관리자 번호 가져오기
    $admin_no = $_SESSION['no_adm_login_no'] ?? null;

    if (!$admin_no) {
        echo json_encode(["result" => "fail", "msg" => "로그인이 필요합니다. 다시 로그인해주세요."]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT a.no, a.uid, a.upwd, a.uname, a.active_status 
                           FROM nb_admin a 
                           WHERE a.no = :no AND a.sitekey = :sitekey");
    $stmt->execute(['no' => $admin_no, 'sitekey' => $NO_SITE_UNIQUE_KEY]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo json_encode(["result" => "fail", "msg" => "비정상적인 접근입니다."]);
        exit;
    } else {
        $db_no = $data['no'];
        $db_pwd = $data['upwd'];

        // password_verify로 현재 비밀번호 검증
        if (!password_verify($pwd_old, $db_pwd)) {
            echo json_encode(["result" => "fail", "msg" => "현재 비밀번호가 일치하지 않습니다. 다시 입력해주세요"]);
            exit;
        }

        if ($pwd_new !== $pwd_new_confirm) {
            echo json_encode(["result" => "fail", "msg" => "변경하시려는 신규 비밀번호와 비밀번호 확인이 서로 일치하지 않습니다."]);
            exit;
        }

        // password_hash로 새 비밀번호 해시
        if (!$isValidPasswordPolicy($pwd_new)) {
            echo json_encode([
                "result" => "fail",
                "msg" => "비밀번호는 8자 이상이어야 하며, 영문 대문자, 영문 소문자, 숫자, 특수문자 중 3가지 이상을 포함해야 합니다."
            ]);
            exit;
        }

        $new_hashed = password_hash($pwd_new, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE nb_admin SET upwd = :new_pwd WHERE no = :no");
        $result = $stmt->execute(['new_pwd' => $new_hashed, 'no' => $db_no]);

        if ($result) {
            $_SESSION = [];

            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $params['path'] ?: '/', $params['domain'] ?? '', (bool)($params['secure'] ?? false), (bool)($params['httponly'] ?? false));
            }

            setcookie('cookie_session_id', '', time() - 42000, '/');
            session_destroy();

            echo json_encode(["result" => "success", "msg" => "정상적으로 수정 되었습니다. 변경된 비밀번호로 새로 로그인해주세요."]);
        } else {
            echo json_encode(["result" => "fail", "msg" => "비밀번호 변경에 실패했습니다. 다시 시도해주세요."]);
        }
    }
}


if ($mode == "robots.update") {
    $role->requireLogin();
    $role->requireCanModify();

    if (!$role->is('superadmin')) {
        echo json_encode(["result" => "fail", "msg" => "robots.txt 관리는 최고 관리자만 사용할 수 있습니다."]);
        exit;
    }

    $robotsContent = isset($_POST['robots_content']) ? (string) $_POST['robots_content'] : '';
    // 빈 본문은 NULL로 저장 → 동적 라우트가 기본 템플릿 사용
    $value = trim($robotsContent) === '' ? null : $robotsContent;

    try {
        $stmt = $pdo->prepare('SELECT no FROM nb_siteinfo WHERE sitekey = :sitekey LIMIT 1');
        $stmt->execute([':sitekey' => $NO_SITE_UNIQUE_KEY]);
        $exists = $stmt->fetchColumn();

        if ($exists) {
            $stmt = $pdo->prepare('UPDATE nb_siteinfo SET robots_content = :rc WHERE sitekey = :sitekey');
            $ok = $stmt->execute([':rc' => $value, ':sitekey' => $NO_SITE_UNIQUE_KEY]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO nb_siteinfo (sitekey, robots_content) VALUES (:sitekey, :rc)');
            $ok = $stmt->execute([':sitekey' => $NO_SITE_UNIQUE_KEY, ':rc' => $value]);
        }

        echo json_encode([
            "result" => $ok ? "success" : "fail",
            "msg" => $ok ? "robots.txt 가 저장되었습니다." : "저장 중 오류가 발생했습니다."
        ]);
    } catch (Exception $e) {
        echo json_encode(["result" => "fail", "msg" => "저장 중 오류: " . $e->getMessage()]);
    }
    exit;
}

if ($mode == "setting.config.save") {
    $role->requireLogin();
    $role->requireCanModify();

    // A partial form must never erase fields that it did not submit.
    $fields = [
        'title', 'phone', 'hp', 'fax', 'email',
        'customercenter_able_time', 'company_able_time', 'google_map_key',
        'footer_name', 'footer_address', 'footer_phone', 'footer_hp',
        'footer_fax', 'footer_email', 'footer_owner', 'footer_ssn', 'footer_policy_charger',
    ];
    $values = [];
    foreach ($fields as $field) {
        if (!array_key_exists($field, $_POST)) {
            continue;
        }
        if (!is_string($_POST[$field])) {
            echo json_encode(['result' => 'fail', 'msg' => '입력값 형식이 올바르지 않습니다.']);
            exit;
        }
        $values[$field] = $_POST[$field];
    }

    foreach (['logo_top', 'logo_footer'] as $field) {
        $file = $_FILES[$field] ?? null;
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            echo json_encode(['result' => 'fail', 'msg' => '로고 업로드에 실패했습니다.']);
            exit;
        }
        // Keep the previous file until the database has accepted the new name.
        $upload = imageUpload($UPLOAD_SITEINFO_DIR_LOGO, $file, '', false);
        if (empty($upload['saved'])) {
            echo json_encode(['result' => 'fail', 'msg' => '로고 업로드에 실패했습니다.']);
            exit;
        }
        $values[$field] = $upload['saved'];
    }

    if (!$values) {
        echo json_encode(['result' => 'fail', 'msg' => '저장할 정보가 없습니다.']);
        exit;
    }

    $stmt = $pdo->prepare('SELECT no FROM nb_siteinfo WHERE sitekey = :sitekey');
    $stmt->execute(['sitekey' => $NO_SITE_UNIQUE_KEY]);
    $exists = $stmt->fetchColumn() !== false;
    if ($exists) {
        // Column names come exclusively from the whitelist above.
        $assignments = array_map(static fn($field) => "$field = :$field", array_keys($values));
        $query = 'UPDATE nb_siteinfo SET ' . implode(', ', $assignments) . ' WHERE sitekey = :sitekey';
    } else {
        $values += array_fill_keys(array_merge($fields, ['logo_top', 'logo_footer']), '');
        $columns = array_keys($values);
        $query = 'INSERT INTO nb_siteinfo (sitekey, ' . implode(', ', $columns) . ') VALUES (:sitekey, :'
            . implode(', :', $columns) . ')';
    }
    $values['sitekey'] = $NO_SITE_UNIQUE_KEY;
    $result = $pdo->prepare($query)->execute($values);
    echo json_encode([
        'result' => $result ? 'success' : 'fail',
        'msg' => $result ? '정상적으로 저장되었습니다.' : '처리중 문제가 발생하였습니다.[Error-DB]',
    ]);
}
