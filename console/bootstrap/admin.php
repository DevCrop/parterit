<?php

include_once __DIR__ . '/base.php';
require_once dirname(__DIR__) . '/lib/Role.php';

if (!isset($role) || !($role instanceof Role)) {
    $role = new Role();
}

if (empty($_SESSION['no_adm_login_uid'])) {
    $loginUrl = rtrim($GLOBALS['NO_ADMIN_BASE'] ?? '', '/') . '/';
    header('Location: ' . $loginUrl);
    exit;
}
