<?php

define('ROOT_PATH', dirname(__DIR__));
define('BASE_PATH', '');

/** 사이트가 하위 경로에 있을 때 URL 접두(예: /shop). 빈 문자열이면 도메인 루트. */
if (!defined('BASE_DIR')) {
    define('BASE_DIR', BASE_PATH);
}

/** 사이트별 설정값 단일 관리 파일 */
require_once __DIR__ . '/site_config.php';

/**
 * 관리자 폴더명 오버라이드: 상수 또는 환경변수 NO_ADMIN_DIR.
 * 미지정 시 기본은 site_config.php 의 SITE_ADMIN_DIR.
 */
if (!defined('NO_ADMIN_DIR')) {
    define('NO_ADMIN_DIR', SITE_ADMIN_DIR);
}

define('IMG_PATH', '/resource/images');

/** 푸터 인스타그램 링크(비우면 아이콘 미노출). DB nb_siteinfo.footer_instagram 컬럼이 있으면 그 값이 우선합니다. */
if (!defined('SITE_INSTAGRAM_URL')) {
    define('SITE_INSTAGRAM_URL', '');
}

define('ENV_PRODUCTION', 'production');
define('ENV_DEVELOPMENT', 'development');

define('FORCE_LOCALE_PREFIX', false);

if (!defined('LOCALES')) {
    $siteSupportedLocales = defined('SITE_SUPPORTED_LOCALES') && is_array(SITE_SUPPORTED_LOCALES)
        ? SITE_SUPPORTED_LOCALES
        : ['ko'];

    $knownLocaleMap = [
        'en' => 'en-US',
        'ko' => 'ko-KR',
        'ja' => 'ja-JP',
        'jp' => 'ja-JP',
        'es' => 'es-ES',
        'pt' => 'pt-PT',
    ];

    $resolvedLocales = [];
    foreach ($siteSupportedLocales as $siteLocale) {
        $siteLocale = (string) $siteLocale;
        if ($siteLocale === '') {
            continue;
        }

        $resolvedLocales[$siteLocale] = $knownLocaleMap[$siteLocale] ?? $siteLocale;
    }

    if ($resolvedLocales === []) {
        $resolvedLocales = ['ko' => 'ko-KR'];
    }

    define('LOCALES', $resolvedLocales);
}

/** 사이트 기본 언어(쿠키·Accept-Language 없을 때) */
define('DEFAULT_LOCALE', defined('SITE_DEFAULT_LOCALE') ? SITE_DEFAULT_LOCALE : 'ko');

/**
 * 환경 판별 (우선순위):
 *  1) 서버측 환경변수 APP_ENV (Apache `SetEnv APP_ENV production` / PHP-FPM env) — 있으면 최우선·스푸핑 안전.
 *  2) 없으면 호스트로 판별 — 로컬 개발 호스트(localhost·루프백·docker)만 development, 그 외 실도메인은 production.
 * → 실서버는 별도 설정 없이 업로드만으로 production 동작.
 * 주의: APP_ENV 미설정 시 실서버에 `Host: localhost` 로 들어오면 dev 로 빠지는 잔여 리스크 →
 *       운영에선 vhost 에 `SetEnv APP_ENV production` 을 넣어 1)로 고정하는 것을 권장.
 */
define('ENV', (function () {
    $e = $_SERVER['APP_ENV'] ?? getenv('APP_ENV');
    if ($e === ENV_PRODUCTION || $e === ENV_DEVELOPMENT) {
        return $e;
    }
    $host = strtolower(explode(':', $_SERVER['HTTP_HOST'] ?? '')[0]);
    $devHosts = ['localhost', '127.0.0.1', '::1', 'web'];
    return ($host === '' || in_array($host, $devHosts, true)) ? ENV_DEVELOPMENT : ENV_PRODUCTION;
})());

/**
 * 운영(ENV_PRODUCTION)에서만 base 부트스트랩 스킴 강제에 사용합니다.
 * - ENV_DEVELOPMENT: 항상 HTTP로 고정(이 값과 무관).
 * - ENV_PRODUCTION + true: SSL/TLS(또는 프록시 HTTPS 종료)가 있을 때만 HTTPS로 리다이렉트.
 * - ENV_PRODUCTION + false: 인증서 없이 HTTP만 쓰는 경우 그대로 HTTP 유지.
 */
define('ENV_HTTPS_ENABLED', false);

/**
 * HTTPS 인증서가 없어 모든 트래픽을 HTTP로 강제 다운그레이드해야 할 때 true.
 * true 이면 ENV / 루프백 / ENV_HTTPS_ENABLED 와 무관하게 https → http 리다이렉트를 건다.
 */
define('FORCE_HTTP_SCHEME', true);

/**
 * Database Configuration
 * -----------------------
 * 로컬/운영 환경 모두에서 DB 접속 정보를 여기서 관리합니다.
 * (실 서버에서는 환경변수나 .env를 통해 덮어씌워 사용 권장)
 */
if (ENV === ENV_PRODUCTION) {
    $productionEnv = [];
    $envFile = ROOT_PATH . '/.env';
    if (is_readable($envFile)) {
        $parsedEnv = parse_ini_file($envFile, false, INI_SCANNER_RAW);
        $productionEnv = is_array($parsedEnv) ? $parsedEnv : [];
    }

    $envValue = static function (string $key, string $fallback = '') use ($productionEnv): string {
        $serverValue = $_SERVER[$key] ?? getenv($key);
        if (is_string($serverValue) && trim($serverValue) !== '') {
            return trim($serverValue);
        }

        return trim((string) ($productionEnv[$key] ?? $fallback));
    };

    $dbHost = $envValue('DB_HOST', '127.0.0.1');
    $dbName = $envValue('DB_NAME');
    $dbUser = $envValue('DB_USER');
    $dbPass = $envValue('DB_PASS');
    if ($dbName === '' || $dbUser === '' || $dbPass === '') {
        throw new RuntimeException('Production database credentials are not configured.');
    }

    define('DB_HOST', $dbHost);
    define('DB_PORT', max(1, (int) $envValue('DB_PORT', '3306')));
    define('DB_NAME', $dbName);
    define('DB_USER', $dbUser);
    define('DB_PASS', $dbPass);
    define('DB_CHARSET', $envValue('DB_CHARSET', 'utf8mb4'));
} else if (ENV === ENV_DEVELOPMENT) {
    define('DB_HOST', 'db');
    define('DB_PORT', 3306);
    define('DB_NAME', 'nineonelabs');
    define('DB_USER', 'user');
    define('DB_PASS', 'password');
    define('DB_CHARSET', 'utf8mb4');
}

/** nb_request.sitekey 등 DB 레코드 구분용 (nb_siteinfo.sitekey 와 동일 권장) */
if (!defined('NB_SITEKEY')) {
    define('NB_SITEKEY', SITE_SITEKEY);
}
