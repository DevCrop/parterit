<?php

function menu_read_json(string $path): array
{
    if ($path === '' || !is_file($path)) {
        return [];
    }

    $content = file_get_contents($path);
    if ($content === false || $content === '') {
        return [];
    }

    $decoded = json_decode($content, true);

    return is_array($decoded) ? $decoded : [];
}

function getJSON($path)
{
    return menu_read_json((string) $path);
}

function show($stuff): void
{
    echo '<pre>';
    var_dump($stuff);
    echo '</pre>';
}

function menu_detect_board_locale(array $boardNumList): ?string
{
    $boardNo = isset($_GET['board_no']) ? (string) $_GET['board_no'] : '';
    if ($boardNo === '') {
        return null;
    }

    foreach ($boardNumList as $lang => $list) {
        foreach ((array) $list as $no) {
            if ($boardNo === (string) $no) {
                return (string) $lang;
            }
        }
    }

    return null;
}

function checkBoardLocale($BOARD_NUM_LIST)
{
    return menu_detect_board_locale((array) $BOARD_NUM_LIST);
}

function menu_is_external_url(string $path): bool
{
    return (bool) preg_match('#^(https?:)?//#i', $path);
}

function menu_current_request_uri(): string
{
    static $cached = null;

    if ($cached !== null) {
        return $cached;
    }

    $requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
    $path = parse_url($requestUri, PHP_URL_PATH);

    $cached = is_string($path) && $path !== '' ? $path : '/';

    return $cached;
}

function getImage($path): string
{
    return 'style="background-image: url(' . IMG_PATH . $path . ')"';
}
