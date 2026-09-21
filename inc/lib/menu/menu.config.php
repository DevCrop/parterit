<?php

$DEFAULT_LANG = function_exists('project_default_locale') ? project_default_locale() : 'ko';
$LOCALE_LIST = [
    'ko' => 'ko-kr',
    'en' => 'en-us',
    'ja' => 'ja-jp',
    'es' => 'es-es',
    'pt' => 'pt-pt',
];

$SERVER_ROOT = defined('ROOT') ? ROOT : dirname(__DIR__, 3);
$ROOT = $NO_IS_SUBDIR;
$CLIENT_ROOT = $ROOT;
$STATIC_ROOT = empty($ROOT) ? $SERVER_ROOT : $SERVER_ROOT . '/' . $ROOT;
$HTML_LANG = $DEFAULT_LANG;

$BOARD_DIR = $ROOT . '/pages/board';
$BOARD_PATH = $BOARD_DIR . '/board.list.php';
$BOARD_NUM_LIST = [];
foreach ((function_exists('project_supported_locales') ? project_supported_locales() : array_keys($LOCALE_LIST)) as $supportedLocale) {
    $BOARD_NUM_LIST[(string) $supportedLocale] = [];
}

$MENU_DIR = $STATIC_ROOT . '/json';

$boardLang = menu_detect_board_locale($BOARD_NUM_LIST);
if (function_exists('project_localization_enabled') && project_localization_enabled() && $boardLang !== null) {
    $HTML_LANG = $boardLang;
    $CLIENT_ROOT = $ROOT . "/lang/{$HTML_LANG}";
}

$requestPath = menu_current_request_uri();
$pathSegments = array_values(array_filter(explode('/', trim($requestPath, '/')), 'strlen'));
if (function_exists('project_localization_enabled')
    && project_localization_enabled()
    && ($pathSegments[0] ?? '') === 'lang'
    && isset($pathSegments[1])) {
    $HTML_LANG = $pathSegments[1];
    $CLIENT_ROOT = $ROOT . "/lang/{$HTML_LANG}";
}

$DIR_LIST = [
    'layouts' => $STATIC_ROOT . '/inc/layouts',
    'lib' => $STATIC_ROOT . '/inc/lib',
    'pages' => $CLIENT_ROOT . '/pages',
    'board' => $STATIC_ROOT . '/pages/board',
    'resource' => $STATIC_ROOT . '/resource',
];

$MENU_PATH = $MENU_DIR . "/menu.{$HTML_LANG}.json";

if (!defined('IMG_PATH')) {
    define('IMG_PATH', $ROOT . '/resource/images');
}

if (!file_exists($MENU_PATH) && $HTML_LANG === $DEFAULT_LANG) {
    $MENU_PATH = $MENU_DIR . '/menu.json';
}

$LOCALE = $LOCALE_LIST[$HTML_LANG] ?? $LOCALE_LIST[$DEFAULT_LANG];

$MENU_CONFIG = [
    'dir_list' => $DIR_LIST,
    'board_path' => $BOARD_PATH,
    'board_num_list' => $BOARD_NUM_LIST,
    'menu_path' => $MENU_PATH,
    'default_lang' => $DEFAULT_LANG,
    'html_lang' => $HTML_LANG,
    'locale' => $LOCALE,
    'root' => $ROOT,
    'client_root' => $CLIENT_ROOT,
    'static_root' => $STATIC_ROOT,
    'server_root' => $SERVER_ROOT,
];
