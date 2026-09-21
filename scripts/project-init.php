<?php

/**
 * Project initialization script.
 *
 * Philosophy: define what to KEEP — everything else is cleared.
 * Each section declares a KEEP list (boilerplate) and removes anything
 * outside that list, so new project-specific additions are automatically
 * wiped without touching this script.
 *
 * Usage: php scripts/project-init.php  (or: make new)
 */

declare(strict_types=1);

$root = dirname(__DIR__);
$ok   = true;

echo "\n=== 신규 프로젝트 초기화 ===\n\n";

// ── 공통 헬퍼: 디렉토리 재귀 삭제 ──────────────────────────────────────────
function init_delete_dir(string $dir): int
{
    if (!is_dir($dir)) {
        return 0;
    }
    $count = 0;
    $it    = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($it as $f) {
        if ($f->isDir()) {
            @rmdir($f->getRealPath());
        } else {
            @unlink($f->getRealPath());
            $count++;
        }
    }
    @rmdir($dir);
    return $count;
}

// ── 공통 헬퍼: 디렉토리 내부 비우기 (폴더·.gitkeep 유지) ──────────────────
function init_clear_dir(string $dir): int
{
    if (!is_dir($dir)) {
        return 0;
    }
    $count = 0;
    $it    = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($it as $f) {
        if ($f->getFilename() === '.gitkeep') {
            continue;
        }
        if ($f->isDir()) {
            @rmdir($f->getRealPath());
        } else {
            @unlink($f->getRealPath());
            $count++;
        }
    }
    if (!file_exists($dir . '/.gitkeep')) {
        file_put_contents($dir . '/.gitkeep', '');
    }
    return $count;
}

// ── 1. config/project_profile.php ──────────────────────────────────────────
$profilePath    = $root . '/config/project_profile.php';
$profileContent = <<<'PHP'
<?php

return [
    'branding' => [
        'display_name' => 'SiteName',  // 화면에 표시되는 사이트명
        'footer_name'  => 'SiteName',  // 법적 표기용 이름
        'sitekey'      => 'SITEKY',    // 영문 대문자 6자 이내 고유 키
    ],
    'admin' => [
        'dir'           => 'console',
        'support_email' => '',
        'support_phone' => '',
        'vendor_name'   => '',
        'vendor_url'    => '',
        'login_caption' => '관리 시스템',
    ],
    'assets' => [
        'logo_header_path' => '/resource/images/logo/logo_w.png',
        'logo_footer_path' => '/resource/images/logo/logo_color.svg',
    ],
    'routes' => [
        'home_path'          => '/',
        'primary_cta_path'   => '/contact',
        'contact_index_path' => '/contact',
    ],
    'localization' => [
        'enabled'           => false,
        'default_locale'    => 'ko',
        'supported_locales' => ['ko'],
    ],
    'features' => [
        'ui'      => ['language_switcher'  => false],
        'content' => ['site_tags'          => true],
        'seo'     => ['alternate_hreflang' => false],
    ],
    'links' => [
        'instagram'     => '',
        'kakao_map'     => '',
        'naver_map'     => '',
        'google_map'    => '',
        'youtube'       => '',
        'blog'          => '',
        'kakao_channel' => '',
    ],
    'contact' => [
        'phone'   => '',
        'email'   => '',
        'address' => '',
    ],
    'mail' => [
        'use_gmail'          => false,
        'gmail_from'         => '',
        'gmail_from_name'    => '',
        'gmail_app_password' => '',
        'notify_to'          => '',
    ],
    'form' => [
        'request_type'   => 'contact_request',
        'notify_subject' => '문의 신청 접수',
        'notify_title'   => '문의 신청이 접수되었습니다',
    ],
];
PHP;

if (file_put_contents($profilePath, $profileContent) !== false) {
    echo "[완료] config/project_profile.php   — 브랜딩·연락처·링크·mail·form 초기화\n";
} else {
    echo "[오류] config/project_profile.php 쓰기 실패\n";
    $ok = false;
}

// ── 2. inc/lib/var.php — 폼 선택지만 비우기 (framework 배열은 유지) ─────────
$varPath    = $root . '/inc/lib/var.php';
$varContent = file_get_contents($varPath);

if ($varContent === false) {
    echo "[오류] inc/lib/var.php 읽기 실패\n";
    $ok = false;
} else {
    $targets = [
        'request_apply_gender_options',
        'request_apply_residence_options',
        'request_apply_interest_options',
        'banner_hall_labels',
    ];
    foreach ($targets as $var) {
        $varContent = preg_replace(
            '/(\$' . preg_quote($var, '/') . '\s*=\s*)\[.*?\];/s',
            '${1}[];',
            $varContent
        );
    }
    if (file_put_contents($varPath, $varContent) !== false) {
        echo "[완료] inc/lib/var.php              — 폼 선택지 4개 배열 비움 (framework 배열 유지)\n";
    } else {
        echo "[오류] inc/lib/var.php 쓰기 실패\n";
        $ok = false;
    }
}

// ── 3. routes/web.php — 공통 라우트만 남기기 ──────────────────────────────
$routesPath    = $root . '/routes/web.php';
$routesContent = <<<'PHP'
<?php

if (!isset($NO_SITE_UNIQUE_KEY)) {
    $NO_SITE_UNIQUE_KEY = "SITEKY";
}

/** @var \Routing\Router $router */
$router = app()->router();

$router->get('/', fn() => render('pages.index'))->name('home');
$router->get('/about', fn() => render('pages.about-us.index'))->name('about-us');
$router->get('/sitemap.xml', function () {
    return response(sitemap_generator()->generateXml(), 200, [
        'Content-Type' => 'application/xml; charset=utf-8',
    ]);
})->name('sitemap');

$router->group(['prefix' => '/contact'], function ($r) {
    $r->get('/', fn() => render('pages.contact.free-asset-diagnosis'))->name('contact');
    $r->post('/', function () {
        require_once ROOT_PATH . '/module/request.php';
        return asset_request_response(app()->request());
    })->name('contact.submit');
});

$router->group(['prefix' => '/legal'], function ($r) {
    $r->get('/privacy', fn() => render('pages.legal.privacy'))->name('legal.privacy');
    $r->get('/marketing', fn() => render('pages.legal.marketing'))->name('legal.marketing');
    $r->get('/cookies', fn() => render('pages.legal.cookies'))->name('legal.cookies');
});
PHP;

if (file_put_contents($routesPath, $routesContent) !== false) {
    echo "[완료] routes/web.php               — 공통 라우트만 유지 (/, /about, /contact/*, /legal/*, /sitemap)\n";
} else {
    echo "[오류] routes/web.php 쓰기 실패\n";
    $ok = false;
}

// ── 4. views/pages/ — KEEP 목록 외 하위 디렉토리 전부 삭제 ──────────────────
//
// KEEP: 보일러플레이트로 유지할 페이지 디렉토리
// 프로젝트 전용 디렉토리(service, professionals, case-studies 등)는
// 목록에 없으면 자동 삭제되므로 이 스크립트를 수정할 필요가 없다.
$keepPageDirs  = ['contact', 'legal', 'about-us'];
$pagesRoot     = $root . '/views/pages';
$deletedPages  = 0;

foreach (scandir($pagesRoot) ?: [] as $entry) {
    if ($entry === '.' || $entry === '..') {
        continue;
    }
    $fullPath = $pagesRoot . '/' . $entry;
    if (is_dir($fullPath) && !in_array($entry, $keepPageDirs, true)) {
        $deletedPages += init_delete_dir($fullPath);
        echo "  [삭제] views/pages/{$entry}/\n";
    }
}

$pageBoilerplate = <<<'PHP'
<?php section('content') ?>

<?php end_section() ?>
PHP;

file_put_contents($pagesRoot . '/index.php', $pageBoilerplate);

if (!is_dir($pagesRoot . '/about-us')) {
    mkdir($pagesRoot . '/about-us', 0755, true);
}
file_put_contents($pagesRoot . '/about-us/index.php', $pageBoilerplate);

echo "[완료] views/pages/                 — KEEP: " . implode(', ', $keepPageDirs) . "; index.php·about-us/index.php 보일러플레이트\n";

// ── 5. resource/scss/pages/ — KEEP 목록 외 .scss 파일 전부 삭제 ──────────────
//
// KEEP: 보일러플레이트 SCSS 파일
// 프로젝트 전용 파일(_case-study-view.scss 등)은
// 목록에 없으면 자동 삭제된다.
$keepScssFiles = ['_main.scss', '_sub.scss', 'index.scss'];
$scssPageDir   = $root . '/resource/scss/pages';
$scssBase      = "@use \"../abstract\" as *;\n";
$deletedScss   = 0;

foreach (glob($scssPageDir . '/*.scss') ?: [] as $scssFile) {
    $filename = basename($scssFile);
    if (!in_array($filename, $keepScssFiles, true)) {
        @unlink($scssFile);
        $deletedScss++;
        echo "  [삭제] resource/scss/pages/{$filename}\n";
    }
}

// _main.scss, _sub.scss → 보일러플레이트만
foreach (['_main.scss', '_sub.scss'] as $f) {
    $p = $scssPageDir . '/' . $f;
    if (file_put_contents($p, $scssBase) === false) {
        echo "[오류] resource/scss/pages/{$f} 쓰기 실패\n";
        $ok = false;
    }
}

// index.scss → KEEP 목록에 맞게 고정 내용으로 덮어쓰기
$scssIndexContent = "@forward \"./main\";\n@forward \"./sub\";\n";
if (file_put_contents($scssPageDir . '/index.scss', $scssIndexContent) === false) {
    echo "[오류] resource/scss/pages/index.scss 쓰기 실패\n";
    $ok = false;
}

echo "[완료] resource/scss/pages/         — KEEP: " . implode(', ', $keepScssFiles) . "; _main·_sub 보일러플레이트\n";

// ── 6. resource/js/ 초기화 ───────────────────────────────────────────────────

// 6-a. resource/js/ui/ — KEEP 목록 외 .js 파일 전부 삭제
//
// KEEP: 범용 UI 유틸리티
// 프로젝트 전용 모듈(AboutStatCounters.js 등)은
// 목록에 없으면 자동 삭제된다.
$keepUiFiles = ['TabManager.js'];
$uiDir       = $root . '/resource/js/ui';
$deletedUi   = 0;

foreach (glob($uiDir . '/*.js') ?: [] as $jsFile) {
    $filename = basename($jsFile);
    if (!in_array($filename, $keepUiFiles, true)) {
        @unlink($jsFile);
        $deletedUi++;
        echo "  [삭제] resource/js/ui/{$filename}\n";
    }
}

echo "[완료] resource/js/ui/              — KEEP: " . implode(', ', $keepUiFiles) . "; 프로젝트 전용 모듈 {$deletedUi}개 삭제\n";

// 6-b. resource/js/app.js — 템플릿으로 교체
//
// scripts/templates/app.js 가 보일러플레이트의 단일 진실 소스(single source of truth).
// 정규식 기반 함수 제거 대신, 템플릿을 그대로 복사한다.
$appTemplatePath = $root . '/scripts/templates/app.js';
$appTargetPath   = $root . '/resource/js/app.js';

if (!file_exists($appTemplatePath)) {
    echo "[오류] scripts/templates/app.js 없음 — app.js 초기화 건너뜀\n";
    $ok = false;
} elseif (copy($appTemplatePath, $appTargetPath)) {
    echo "[완료] resource/js/app.js           — 보일러플레이트 템플릿으로 교체\n";
} else {
    echo "[오류] resource/js/app.js 복사 실패\n";
    $ok = false;
}

// ── 7. resource/dist/ — 빌드 결과물 삭제 ────────────────────────────────────
$distFiles   = ['app.js', 'app.js.map', 'style.css', 'style.css.map'];
$deletedDist = 0;
foreach ($distFiles as $f) {
    $p = $root . '/resource/dist/' . $f;
    if (file_exists($p)) {
        @unlink($p);
        $deletedDist++;
    }
}
echo "[완료] resource/dist/               — 빌드 결과물 {$deletedDist}개 삭제\n";

// ── 8. bootstrap/menu.php — 공통 메뉴만 남기기 ──────────────────────────────
$menuPath    = $root . '/bootstrap/menu.php';
$menuContent = <<<'PHP'
<?php

use Menu\Menu;

$menu = new Menu('site');
app()->share('menu', $menu);

$menu->add('ABOUT US', '/about', 'about-us', ['orderIndex' => 10]);

$menu->add('CONTACT', '/contact', 'contact', ['orderIndex' => 20]);
PHP;

if (file_put_contents($menuPath, $menuContent) !== false) {
    echo "[완료] bootstrap/menu.php           — about·contact만 유지\n";
} else {
    echo "[오류] bootstrap/menu.php 쓰기 실패\n";
    $ok = false;
}

// ── 9. config/setup.php — 운영 DB 자격증명 초기화 ───────────────────────────
$setupPath    = $root . '/config/setup.php';
$setupContent = file_get_contents($setupPath);
if ($setupContent !== false) {
    $setupContent = preg_replace(
        "/define\('ENV',\s*ENV_PRODUCTION\)/",
        "define('ENV', ENV_DEVELOPMENT)",
        $setupContent
    );
    $setupContent = preg_replace(
        "/if \(ENV === ENV_PRODUCTION\) \{.*?\}/s",
        "if (ENV === ENV_PRODUCTION) {\n    define('DB_HOST', '');\n    define('DB_PORT', 3306);\n    define('DB_NAME', '');\n    define('DB_USER', '');\n    define('DB_PASS', '');\n    define('DB_CHARSET', 'utf8mb4');\n}",
        $setupContent
    );
    if (file_put_contents($setupPath, $setupContent) !== false) {
        echo "[완료] config/setup.php             — ENV_DEVELOPMENT 고정, 운영 DB 자격증명 초기화\n";
    } else {
        echo "[오류] config/setup.php 쓰기 실패\n";
        $ok = false;
    }
} else {
    echo "[오류] config/setup.php 읽기 실패\n";
    $ok = false;
}

// ── 10. config/variables.php — 로고 경로 초기화 ────────────────────────────
$varsPath    = $root . '/config/variables.php';
$varsContent = <<<'PHP'
<?php

define('LOGO_HEADER', BASE_PATH . '/resource/images/logo/logo_w.png');
define('LOGO_FOOTER', BASE_PATH . '/resource/images/logo/logo_color.svg');

// Scroll Animation — AOS 기본 속성
if (!defined('AOS_TITLE')) {
    define('AOS_TITLE', 'data-aos="fade-up" data-aos-duration="1200"');
}
if (!defined('AOS_CONTENTS')) {
    define('AOS_CONTENTS', 'data-aos="fade-up" data-aos-duration="1600"');
}
PHP;

if (file_put_contents($varsPath, $varsContent) !== false) {
    echo "[완료] config/variables.php         — 로고 경로 초기화\n";
} else {
    echo "[오류] config/variables.php 쓰기 실패\n";
    $ok = false;
}

// ── 11. resource/scss/abstract/ 초기화 ───────────────────────────────────────

// 11-a. _colors.scss — primary 색상 초기화, subway·bus 제거
$colorsPath    = $root . '/resource/scss/abstract/_colors.scss';
$colorsContent = file_get_contents($colorsPath);
if ($colorsContent !== false) {
    $colorsContent = str_replace("\r\n", "\n", $colorsContent);

    $oldPrimary    = "  primary-50: #f2f3ff,\n  primary-100: #e7e8ff,\n  primary-200: #d3d5ff,\n  primary-300: #afb0ff,\n  primary-400: #8783ff,\n  primary-500: #5f50ff,\n  primary-600: #4b2cfb,\n  primary-700: #3d1ae7,\n  primary-800: #3617ce,\n  primary-900: #2c149e,\n  primary-950: #16096c,\n  primary-def: #3617ce,";
    $newPrimary    = "  primary-50: #eff6ff,\n  primary-100: #dbeafe,\n  primary-200: #bfdbfe,\n  primary-300: #93c5fd,\n  primary-400: #60a5fa,\n  primary-500: #3b82f6,\n  primary-600: #2563eb,\n  primary-700: #1d4ed8,\n  primary-800: #1e40af,\n  primary-900: #1e3a8a,\n  primary-950: #172554,\n  primary-def: #2563eb,";
    $colorsContent = str_replace($oldPrimary, $newPrimary, $colorsContent);

    $colorsContent = preg_replace('/  subway-line-2:.*?(?=  background-header)/s', '', $colorsContent);

    if (file_put_contents($colorsPath, $colorsContent) !== false) {
        echo "[완료] resource/scss/abstract/_colors.scss — primary 중립 블루, subway·bus 제거\n";
    } else {
        echo "[오류] _colors.scss 쓰기 실패\n";
        $ok = false;
    }
}

// 11-b. _variables.scss — 서체 초기화 (프로젝트 전용 serif 제거)
$variablesPath    = $root . '/resource/scss/abstract/_variables.scss';
$variablesContent = file_get_contents($variablesPath);
if ($variablesContent !== false) {
    $variablesContent = str_replace('"Noto Serif KR"', '"Pretendard"', $variablesContent);
    if (file_put_contents($variablesPath, $variablesContent) !== false) {
        echo "[완료] resource/scss/abstract/_variables.scss — 서체 Pretendard 단일화\n";
    } else {
        echo "[오류] _variables.scss 쓰기 실패\n";
        $ok = false;
    }
}

// 11-c. base/_root.scss — iti hover 색상 새 primary로 업데이트
$rootScssPath    = $root . '/resource/scss/base/_root.scss';
$rootScssContent = file_get_contents($rootScssPath);
if ($rootScssContent !== false) {
    $rootScssContent = str_replace(
        '--iti-hover-color: rgba(42, 105, 77, 0.08);',
        '--iti-hover-color: rgba(37, 99, 235, 0.08);',
        $rootScssContent
    );
    file_put_contents($rootScssPath, $rootScssContent);
    echo "[완료] resource/scss/base/_root.scss— iti hover 색상 업데이트\n";
}

// ── 12. resource/images/ 초기화 ─────────────────────────────────────────────
$imagesRoot    = $root . '/resource/images';
$deletedImages = 0;

$deletedImages += init_delete_dir($imagesRoot . '/main');
$deletedImages += init_delete_dir($imagesRoot . '/sub');
$deletedImages += init_clear_dir($imagesRoot . '/logo');

foreach (glob($imagesRoot . '/icon_*.svg') ?: [] as $f) {
    @unlink($f);
    $deletedImages++;
}

echo "[완료] resource/images/             — main·sub 삭제, logo 초기화 ({$deletedImages}개)\n";

// ── 13. resource/video/ 초기화 ──────────────────────────────────────────────
$videoDir     = $root . '/resource/video';
$deletedVideo = 0;
if (is_dir($videoDir)) {
    foreach (glob($videoDir . '/*.{mp4,webm,mov,avi,mkv}', GLOB_BRACE) ?: [] as $f) {
        @unlink($f);
        $deletedVideo++;
    }
    if (!file_exists($videoDir . '/.gitkeep')) {
        file_put_contents($videoDir . '/.gitkeep', '');
    }
}
echo "[완료] resource/video/              — 동영상 {$deletedVideo}개 삭제\n";

// ── 완료 ────────────────────────────────────────────────────────────────────
echo "\n";
if ($ok) {
    echo "초기화 완료.\n";
    echo "다음: INIT.md 단계 1부터 진행하여 새 프로젝트 값을 채우세요.\n";
} else {
    echo "일부 항목이 실패했습니다. 위의 [오류] 메시지를 확인하세요.\n";
}
echo "\n";
