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

    // Default values for missing POST data
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $hp = isset($_POST['hp']) ? $_POST['hp'] : '';
    $fax = isset($_POST['fax']) ? $_POST['fax'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $customercenter_able_time = isset($_POST['customercenter_able_time']) ? $_POST['customercenter_able_time'] : '';
    $company_able_time = isset($_POST['company_able_time']) ? $_POST['company_able_time'] : '';
    $google_map_key = isset($_POST['google_map_key']) ? $_POST['google_map_key'] : '';
    $footer_name = isset($_POST['footer_name']) ? $_POST['footer_name'] : '';
    $footer_address = isset($_POST['footer_address']) ? $_POST['footer_address'] : '';
    $footer_phone = isset($_POST['footer_phone']) ? $_POST['footer_phone'] : '';
    $footer_hp = isset($_POST['footer_hp']) ? $_POST['footer_hp'] : '';
    $footer_fax = isset($_POST['footer_fax']) ? $_POST['footer_fax'] : '';
    $footer_email = isset($_POST['footer_email']) ? $_POST['footer_email'] : '';
    $footer_owner = isset($_POST['footer_owner']) ? $_POST['footer_owner'] : '';
    $footer_ssn = isset($_POST['footer_ssn']) ? $_POST['footer_ssn'] : '';
    $footer_policy_charger = isset($_POST['footer_policy_charger']) ? $_POST['footer_policy_charger'] : '';
    // Handle file uploads with checks
    $logo_top_filename = isset($_POST['logo_top_filename']) ? $_POST['logo_top_filename'] : '';
    $logo_footer_filename = isset($_POST['logo_footer_filename']) ? $_POST['logo_footer_filename'] : '';

    $dir_logo = $UPLOAD_SITEINFO_DIR_LOGO;

    $logo_top = '';
    $logo_footer = '';

    // Handle logo file uploads
    if (isset($_FILES['logo_top'])) {
        $uploadResult = imageUpload($dir_logo, $_FILES['logo_top'], $logo_top_filename, false);
        $logo_top = $uploadResult['saved'];
    }

    if (isset($_FILES['logo_footer'])) {
        $uploadResult = imageUpload($dir_logo, $_FILES['logo_footer'], $logo_footer_filename, false);
        $logo_footer = $uploadResult['saved'];
    }

    // Check if entry exists in the database
    $stmt = $pdo->prepare("SELECT a.no FROM nb_siteinfo a WHERE a.sitekey = :sitekey");
    $stmt->execute(['sitekey' => $NO_SITE_UNIQUE_KEY]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        // Update existing record (사이트 정보 + 푸터 + 로고만, meta 영역은 SEO 관리에서 처리)
        $query = "UPDATE nb_siteinfo SET
                    title = :title,
                    phone = :phone,
                    hp = :hp,
                    fax = :fax,
                    email = :email,
                    customercenter_able_time = :customercenter_able_time,
                    company_able_time = :company_able_time,
                    google_map_key = :google_map_key,
                    footer_name = :footer_name,
                    footer_address = :footer_address,
                    footer_phone = :footer_phone,
                    footer_hp = :footer_hp,
                    footer_fax = :footer_fax,
                    footer_email = :footer_email,
                    footer_owner = :footer_owner,
                    footer_ssn = :footer_ssn,
                    footer_policy_charger = :footer_policy_charger";

        if ($logo_top) $query .= ", logo_top = :logo_top";
        if ($logo_footer) $query .= ", logo_footer = :logo_footer";

        $query .= " WHERE sitekey = :sitekey";

        $stmt = $pdo->prepare($query);
        $params = compact(
            'title',
            'phone',
            'hp',
            'fax',
            'email',
            'customercenter_able_time',
            'company_able_time',
            'google_map_key',
            'footer_name',
            'footer_address',
            'footer_phone',
            'footer_hp',
            'footer_fax',
            'footer_email',
            'footer_owner',
            'footer_ssn',
            'footer_policy_charger'
        );
        $params['sitekey'] = $NO_SITE_UNIQUE_KEY;
        if ($logo_top) $params['logo_top'] = $logo_top;
        if ($logo_footer) $params['logo_footer'] = $logo_footer;

        $result = $stmt->execute($params);

        echo json_encode(["result" => $result ? "success" : "fail", "msg" => $result ? "정상적으로 수정되었습니다." : "처리중 문제가 발생하였습니다.[Error-DB]"]);
    } else {
        // Insert new record (meta 영역은 SEO 관리에서 처리)
        $query = "INSERT INTO nb_siteinfo (
                    sitekey, title, logo_top, logo_footer,
                    phone, hp, fax, email, customercenter_able_time, company_able_time,
                    google_map_key, footer_name, footer_address, footer_phone, footer_hp,
                    footer_fax, footer_email, footer_owner, footer_ssn, footer_policy_charger
                  ) VALUES (
                    :sitekey, :title, :logo_top, :logo_footer,
                    :phone, :hp, :fax, :email, :customercenter_able_time, :company_able_time,
                    :google_map_key, :footer_name, :footer_address, :footer_phone, :footer_hp,
                    :footer_fax, :footer_email, :footer_owner, :footer_ssn, :footer_policy_charger
                  )";

        $stmt = $pdo->prepare($query);
        $params = compact(
            'title',
            'logo_top',
            'logo_footer',
            'phone',
            'hp',
            'fax',
            'email',
            'customercenter_able_time',
            'company_able_time',
            'google_map_key',
            'footer_name',
            'footer_address',
            'footer_phone',
            'footer_hp',
            'footer_fax',
            'footer_email',
            'footer_owner',
            'footer_ssn',
            'footer_policy_charger'
        );
        $params['sitekey'] = $NO_SITE_UNIQUE_KEY;

        $result = $stmt->execute($params);
        echo json_encode(["result" => $result ? "success" : "fail", "msg" => $result ? "정상적으로 등록 되었습니다." : "처리중 문제가 발생하였습니다.[Error-DB]"]);
    }
}
