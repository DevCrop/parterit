<?php

/**
 * 프로젝트 전용 설정 어댑터.
 *
 * 실제 프로젝트별 값은 `config/project_profile.php`에서 관리하고,
 * 이 파일은 기존 상수 기반 코드와의 호환 계층만 담당합니다.
 */

$projectProfile = require __DIR__ . '/project_profile.php';

if (!defined('PROJECT_PROFILE')) {
    define('PROJECT_PROFILE', $projectProfile);
}

$projectName = isset($projectProfile['branding']['display_name'])
    ? trim((string) $projectProfile['branding']['display_name'])
    : '';

$projectSiteKey = isset($projectProfile['branding']['sitekey'])
    ? trim((string) $projectProfile['branding']['sitekey'])
    : '';

$adminDir = isset($projectProfile['admin']['dir'])
    ? trim((string) $projectProfile['admin']['dir'])
    : '';

$instagramUrl = isset($projectProfile['links']['instagram'])
    ? trim((string) $projectProfile['links']['instagram'])
    : '';

$primaryCtaPath = isset($projectProfile['routes']['primary_cta_path'])
    ? trim((string) $projectProfile['routes']['primary_cta_path'])
    : '';

$headerLogoPath = isset($projectProfile['assets']['logo_header_path'])
    ? trim((string) $projectProfile['assets']['logo_header_path'])
    : '';

$footerLogoPath = isset($projectProfile['assets']['logo_footer_path'])
    ? trim((string) $projectProfile['assets']['logo_footer_path'])
    : '';

$kakaoMapUrl = isset($projectProfile['links']['kakao_map'])
    ? trim((string) $projectProfile['links']['kakao_map'])
    : '';

$supportEmail = isset($projectProfile['admin']['support_email'])
    ? trim((string) $projectProfile['admin']['support_email'])
    : '';

$supportPhone = isset($projectProfile['admin']['support_phone'])
    ? trim((string) $projectProfile['admin']['support_phone'])
    : '';

$vendorName = isset($projectProfile['admin']['vendor_name'])
    ? trim((string) $projectProfile['admin']['vendor_name'])
    : '';

$vendorUrl = isset($projectProfile['admin']['vendor_url'])
    ? trim((string) $projectProfile['admin']['vendor_url'])
    : '';

$localizationEnabled = isset($projectProfile['localization']['enabled'])
    ? (bool) $projectProfile['localization']['enabled']
    : true;

$defaultLocale = isset($projectProfile['localization']['default_locale'])
    ? trim((string) $projectProfile['localization']['default_locale'])
    : '';

$supportedLocales = isset($projectProfile['localization']['supported_locales']) && is_array($projectProfile['localization']['supported_locales'])
    ? array_values(array_filter(array_map('strval', $projectProfile['localization']['supported_locales']), 'strlen'))
    : [];

$projectFeatures = isset($projectProfile['features']) && is_array($projectProfile['features'])
    ? $projectProfile['features']
    : [];

if (!defined('SITE_NAME')) {
    define('SITE_NAME', $projectName !== '' ? $projectName : 'Site');
}

if (!defined('SITE_SITEKEY')) {
    define('SITE_SITEKEY', $projectSiteKey !== '' ? $projectSiteKey : 'SITE');
}

if (!defined('SITE_ADMIN_DIR')) {
    define('SITE_ADMIN_DIR', $adminDir !== '' ? $adminDir : 'console');
}

if (!defined('SITE_INSTAGRAM_URL')) {
    define('SITE_INSTAGRAM_URL', $instagramUrl);
}

if (!defined('SITE_PRIMARY_CTA_PATH')) {
    define('SITE_PRIMARY_CTA_PATH', $primaryCtaPath !== '' ? $primaryCtaPath : '/contact');
}

if (!defined('SITE_HEADER_LOGO_PATH')) {
    define('SITE_HEADER_LOGO_PATH', $headerLogoPath);
}

if (!defined('SITE_FOOTER_LOGO_PATH')) {
    define('SITE_FOOTER_LOGO_PATH', $footerLogoPath);
}

if (!defined('SITE_KAKAO_MAP_URL')) {
    define('SITE_KAKAO_MAP_URL', $kakaoMapUrl);
}

if (!defined('SITE_SUPPORT_EMAIL')) {
    define('SITE_SUPPORT_EMAIL', $supportEmail);
}

if (!defined('SITE_SUPPORT_PHONE')) {
    define('SITE_SUPPORT_PHONE', $supportPhone);
}

if (!defined('SITE_VENDOR_NAME')) {
    define('SITE_VENDOR_NAME', $vendorName);
}

if (!defined('SITE_VENDOR_URL')) {
    define('SITE_VENDOR_URL', $vendorUrl);
}

if (!defined('SITE_I18N_ENABLED')) {
    define('SITE_I18N_ENABLED', $localizationEnabled);
}

if (!defined('SITE_DEFAULT_LOCALE')) {
    define('SITE_DEFAULT_LOCALE', $defaultLocale !== '' ? $defaultLocale : 'ko');
}

if (!defined('SITE_SUPPORTED_LOCALES')) {
    define('SITE_SUPPORTED_LOCALES', !empty($supportedLocales) ? $supportedLocales : ['ko']);
}

if (!defined('SITE_FEATURES')) {
    define('SITE_FEATURES', $projectFeatures);
}
