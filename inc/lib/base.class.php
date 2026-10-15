<?php

if (!function_exists('no_render_internal_error')) {
    function no_render_internal_error(string $message = 'Internal Server Error', int $statusCode = 500): void
    {
        if (PHP_SAPI !== 'cli') {
            if (!headers_sent()) {
                http_response_code($statusCode);
                header('Content-Type: text/plain; charset=UTF-8');
            }
        }

        echo $message;
        exit;
    }
}

if (!function_exists('no_handle_unhandled_exception')) {
    function no_handle_unhandled_exception(Throwable $e): void
    {
        error_log('[unhandled] ' . $e->getMessage());
        no_render_internal_error();
    }
}

if (!function_exists('no_handle_fatal_error')) {
    function no_handle_fatal_error(): void
    {
        $error = error_get_last();
        if ($error === null) {
            return;
        }

        $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
        if (!in_array($error['type'] ?? 0, $fatalTypes, true)) {
            return;
        }

        error_log('[fatal] ' . ($error['message'] ?? 'Unknown fatal error'));
        no_render_internal_error();
    }
}
// 프로젝트 루트 경로 찾기 (src/autoload.php 파일을 찾을 때까지 상위 디렉토리 탐색)
$projectRoot = null;
$currentDir = __DIR__; // inc/lib
$previousDir = '';

while ($currentDir !== $previousDir) {
    $autoloadPath = $currentDir . '/src/autoload.php';
    if (file_exists($autoloadPath)) {
        $projectRoot = $currentDir;
        break;
    }
    $previousDir = $currentDir;
    $currentDir = dirname($currentDir);
}

// 찾지 못한 경우 기본값 사용 (inc/lib에서 2단계 위)
if ($projectRoot === null) {
    $projectRoot = dirname(__DIR__, 2);
}

$autoloadPath = $projectRoot . '/src/autoload.php';

if (!file_exists($autoloadPath)) {
    error_log('[bootstrap] autoload.php not found.');
    no_render_internal_error();
}

include_once $autoloadPath;

if (!isset($isDevelopmentEnv)) {
    $isDevelopmentEnv = (
        defined('ENV') &&
        defined('ENV_DEVELOPMENT') &&
        ENV === ENV_DEVELOPMENT
    );
}

// Scheme handling is intentionally passive: if the request arrives over HTTPS,
// links resolve to https; if it arrives over HTTP, links stay http. No forced
// upgrade or downgrade redirect at the bootstrap layer.

$_path_str = __DIR__;
$CACHE_MODIFIER = date('YmdHis');
$NO_PROJECT_ROOT = str_replace('\\', '/', rtrim($projectRoot, DIRECTORY_SEPARATOR));
$NO_PUBLIC_ROOT = $NO_PROJECT_ROOT;

if (!defined('ROOT')) {
    define('ROOT', $NO_PUBLIC_ROOT);
}

if (!defined('BASE_DIR')) {
    define('BASE_DIR', '');
}

$NO_IS_SUBDIR = BASE_DIR;
$NO_WEB_BASE = rtrim($NO_IS_SUBDIR, '/');

if (!function_exists('no_resolve_admin_directory')) {
    /**
     * 프로젝트 루트 기준 관리자 패널 폴더명.
     * 우선순위: 환경변수 NO_ADMIN_DIR > 상수 NO_ADMIN_DIR > 후보명(console/admin/evercura-gate) 자동 탐색.
     */
    function no_resolve_admin_directory(string $projectRoot): string
    {
        $projectRoot = str_replace('\\', '/', rtrim($projectRoot, '/\\'));
        if ($projectRoot === '') {
            return '';
        }

        $candidates = [];
        $env = getenv('NO_ADMIN_DIR');
        if (is_string($env) && $env !== '') {
            $candidates[] = trim(str_replace('\\', '/', $env), '/');
        }
        if (defined('NO_ADMIN_DIR')) {
            $fromConst = (string) constant('NO_ADMIN_DIR');
            if ($fromConst !== '') {
                $candidates[] = trim(str_replace('\\', '/', $fromConst), '/');
            }
        }

        $candidates = array_merge($candidates, ['console', 'admin', 'evercura-gate']);

        foreach (array_unique($candidates) as $dir) {
            if ($dir === '' || strpos($dir, '..') !== false) {
                continue;
            }
            if (is_file($projectRoot . '/' . $dir . '/lib/Role.php')) {
                return $dir;
            }
        }

        return '';
    }
}

if (!function_exists('no_admin_pages_base_from_script')) {
    /**
     * SCRIPT_NAME에서 …/pages/ 앞까지를 관리자 pages 베이스 URL로 추정 ($NO_ADMIN_PAGES_BASE 미설정 시 보조).
     */
    function no_admin_pages_base_from_script(): string
    {
        $script = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
        if ($script === '') {
            return '';
        }
        $pos = strpos($script, '/pages/');
        if ($pos === false) {
            return '';
        }
        $base = substr($script, 0, $pos + strlen('/pages'));

        return rtrim($base, '/') ?: '';
    }
}

$NO_ADMIN_DIR = no_resolve_admin_directory($NO_PROJECT_ROOT);
if ($NO_ADMIN_DIR === '') {
    error_log('[bootstrap] 관리자 디렉터리를 찾을 수 없습니다. NO_ADMIN_DIR(환경변수·setup.php)를 설정하거나 console/admin/lib/Role.php 중 실제 관리자 경로가 존재해야 합니다.');
    no_render_internal_error('관리자 경로를 초기화할 수 없습니다.');
}

$NO_ADMIN_BASE = ($NO_WEB_BASE !== '' ? $NO_WEB_BASE . '/' : '/') . $NO_ADMIN_DIR;
$NO_ADMIN_PATH = $NO_PROJECT_ROOT . "/" . $NO_ADMIN_DIR;
$NO_ADMIN_PAGES_BASE = $NO_ADMIN_BASE . "/pages";
$NO_ADMIN_RESOURCE_BASE = $NO_ADMIN_BASE . "/resource";
$NO_SITE_UNIQUE_KEY = "DKHOLD";

// 세션이 시작되지 않았을 때만 시작
if (session_status() === PHP_SESSION_NONE) {
    session_name('PARTNERITSESSID');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
// ── 캐시/헤더는 세션 시작 전에 설정 ─────────────────────────────
// if (session_status() !== PHP_SESSION_ACTIVE) {
//     // session_cache_limiter('nocache');
// }

// // 헤더는 아직 전송되지 않았을 때만
// if (!headers_sent()) {
//     header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
//     header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
//     header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
//     header("Cache-Control: post-check=0, pre-check=0", false);
//     header("Pragma: no-cache");
// }

// // 세션도 가드 걸어서 단 한 번만 시작
// if (session_status() !== PHP_SESSION_ACTIVE) {
//     // session_start();
// }


// 현재 도메인 기반 라이선스 키 생성 (호스트 없으면 빈 문자열 처리)
$NO_CURRENT_URL = $_SERVER['HTTP_HOST'] ?? '';
$NO_MAKE_KEY = hash("sha256", $NO_CURRENT_URL . ($NO_SITE_UNIQUE_KEY ?? ''));

// 에러 표시(운영 전환 시 display_errors 0 권장)
error_reporting(E_ALL & ~(E_NOTICE | E_USER_NOTICE | E_WARNING | E_COMPILE_WARNING | E_CORE_WARNING | E_USER_WARNING | E_DEPRECATED | E_USER_DEPRECATED));
ini_set('display_errors', $isDevelopmentEnv ? '1' : '0');
ini_set('display_startup_errors', $isDevelopmentEnv ? '1' : '0');
ini_set('log_errors', '1');

if (!$isDevelopmentEnv && PHP_SAPI !== 'cli') {
    set_exception_handler('no_handle_unhandled_exception');
    register_shutdown_function('no_handle_fatal_error');
}

// 현재 스크립트/경로 정보
$EX_FILENAME = explode("/", $_SERVER['SCRIPT_FILENAME'] ?? '');
$CURR_FILENAME = $EX_FILENAME[count($EX_FILENAME) - 1] ?? '';
$pathParts = explode('/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$END_FILENAME = end($pathParts) ?: '';

$PHP_SELF = $_SERVER['PHP_SELF'] ?? '';

// 서버 업로드 허용 용량
$MAX_SERVER_UPLOAD_SIZE = ini_get('upload_max_filesize') ?: '설정값없음';

// 게시판 기본값
$BOARD_DEFAULT_LIST_SIZE = 20;
$BOARD_DEFULAT_BLOCK_SIZE = 10; // 페이지네이션 블록 크기

// 확장 필드 개수
$NO_EXTRA_FIELDS_COUNT = 15;

// 업로드 경로
$UPLOAD_DIR_BASE = $NO_PROJECT_ROOT . "/uploads";
$UPLOAD_WDIR_BASE = ($NO_WEB_BASE !== '' ? $NO_WEB_BASE : '') . "/uploads";

$UPLOAD_DIR_BANNER = $UPLOAD_DIR_BASE . "/banners";
$UPLOAD_WDIR_BANNER = $UPLOAD_WDIR_BASE . "/banners";

$UPLOAD_DIR_POPUP = $UPLOAD_DIR_BASE . "/popups";
$UPLOAD_WDIR_POPUP = $UPLOAD_WDIR_BASE . "/popups";

$UPLOAD_DIR_BOARD = $UPLOAD_DIR_BASE . "/board";
$UPLOAD_WDIR_BOARD = $UPLOAD_WDIR_BASE . "/board";

$UPLOAD_SITEINFO_DIR_LOGO = $UPLOAD_DIR_BASE . "/logo";
$UPLOAD_SITEINFO_WDIR_LOGO = $UPLOAD_WDIR_BASE . "/logo";

$UPLOAD_META_DIR = $UPLOAD_DIR_BASE . "/meta";
$UPLOAD_META_WDIR = $UPLOAD_WDIR_BASE . "/meta";

$UPLOAD_DIR_EMPLOYMENT = $UPLOAD_DIR_BASE . "/employment";
$UPLOAD_WDIR_EMPLOYMENT = $UPLOAD_WDIR_BASE . "/employment";

$UPLOAD_DIR_ADMISSION = $UPLOAD_DIR_BASE . "/admission";
$UPLOAD_WDIR_ADMISSION = $UPLOAD_WDIR_BASE . "/admission";

$UPLOAD_DIR_REQUEST = $UPLOAD_DIR_BASE . "/request";
$UPLOAD_WDIR_REQUEST = $UPLOAD_WDIR_BASE . "/request";

// 관리자 메뉴 표시 제어
$NO_ADMIN_GNB_BOARD_OPEN   = true;  // 게시판
$NO_ADMIN_GNB_DESIGN_OPEN  = true;  // 디자인
$NO_ADMIN_GNB_REQUEST_OPEN = true;  // 문의/요청
$NO_ADMIN_GNB_SMS_OPEN     = false; // 문자
$NO_ADMIN_GNB_SETTING_OPEN = true;  // 설정
$NO_ADMIN_GNB_LOG_OPEN     = true;  // 로그
$NO_ADMIN_GNB_MEMBER_OPEN  = false;

$NO_ADMIN_LNB_BOARD_MENU_OPEN      = false; // 게시판 메뉴
$NO_ADMIN_LNB_BOARD_MENU_ROLE_OPEN = false; // 게시판 권한 메뉴


// 개발자 IP(관리 메뉴 강제 오픈)
$devIArrIP = ["220.72.73.182", "125.128.228.224", "1.228.9.177"];
$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'] ?? '';
if (in_array($REMOTE_ADDR, $devIArrIP, true)) {
    $NO_ADMIN_LNB_BOARD_MENU_OPEN = true;
    $NO_ADMIN_LNB_BOARD_MENU_ROLE_OPEN = true;
    $NO_ADMIN_GNB_MEMBER_OPEN = true;
}

/*
테이블 요약
nb_admin, nb_siteinfo,
nb_board, nb_board_manage, nb_board_lev_manage,
nb_banner, nb_popup, nb_request,
nb_counter, nb_counter_config, nb_counter_data, nb_counter_route,
nb_member, nb_member_level
*/

// ── 세션/쿠키 안전 접근 ──────────────────────────────────────────
$NO_USR_NO    = $_SESSION['no_usr_no']   ?? 0;
$NO_USR_ID    = $_SESSION['no_usr_id']   ?? '';
$NO_USR_NAME  = $_SESSION['no_usr_name'] ?? '';
$NO_USR_LEV   = $_SESSION['no_usr_lev']  ?? 0;

$NO_ADM_NO    = $_SESSION['no_adm_login_no']   ?? 0;
$NO_ADM_ID    = $_SESSION['no_adm_login_uid']  ?? '';
$NO_ADM_NAME  = $_SESSION['no_adm_login_uname'] ?? '';

$NO_USR_SESSION_ID = session_id();
$NO_USR_SESSION_ID_COOKIE = $_COOKIE['cookie_session_id'] ?? null;
if ($NO_USR_SESSION_ID_COOKIE) {
    $NO_USR_SESSION_ID = $NO_USR_SESSION_ID_COOKIE;
}

// ── PSR-4 유사 오토로더(로컬 classes 경로) ─────────────────────
spl_autoload_register(function ($class) {
    $classPath = __DIR__ . '/classes/' . str_replace('\\', '/', $class) . '.php';
    if (is_file($classPath)) {
        require_once $classPath;
    }
});

// 의존 파일 로드
include_once $_path_str . '/PasswordHashClass.php';
include_once $_path_str . '/db.php';
include_once $_path_str . '/func.php';
include_once $_path_str . '/board.class.php';
// Optional site extension; this project does not ship an inc.php override.
if (is_file($_path_str . '/inc.php')) {
    include_once $_path_str . '/inc.php';
}
include_once $_path_str . '/cache.inc.php';
include_once $_path_str . '/site.info.php';
include_once $_path_str . '/var.php';
include_once $_path_str . '/license.php';
include_once $_path_str . '/Helper.php';
include_once $_path_str . '/SummerNote.php';

// 카운터: /admin/ 경로가 아니면 실행
// 라이선스 검증 (고객 직접 호스팅용 — 검증 비활성)
// if (!in_array($NO_MAKE_KEY, Lisence::getAll(), true)) {
//     echo "라이센스가 확인되지 않았습니다.";
//     exit;
// }


// 코어 설정
include_once $_path_str . '/menu/menu.init.php';
include_once dirname(__DIR__) . '/core/config.php';
include_once dirname(__DIR__) . '/core/util.php';
include_once dirname(__DIR__) . '/core/variables.php';

// 필요 시 버퍼 종료
// ob_end_flush();

// ROLE 검사
