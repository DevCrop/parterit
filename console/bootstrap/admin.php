<?php

include_once __DIR__ . '/base.php';
require_once dirname(__DIR__) . '/lib/Role.php';

// A role/status change must apply to an already signed-in browser as well.
if (!empty($_SESSION['no_adm_login_uid'])) {
    $authStmt = DB::getInstance()->prepare(
        'SELECT no, uid, uname, role_id, active_status FROM nb_admin WHERE no = :no AND sitekey = :sitekey LIMIT 1'
    );
    $authStmt->execute([
        'no' => (int) ($_SESSION['no_adm_login_no'] ?? 0),
        'sitekey' => $NO_SITE_UNIQUE_KEY,
    ]);
    $authAccount = $authStmt->fetch(PDO::FETCH_ASSOC);
    $authRoleId = (int) ($authAccount['role_id'] ?? 0);
    if (!$authAccount || ($authAccount['active_status'] ?? '') !== 'Y' || !isset($admin_roles[$authRoleId])) {
        foreach (['no_adm_login_no', 'no_adm_login_uid', 'no_adm_login_uname', 'no_adm_login_role_id', 'no_adm_login_role'] as $authKey) {
            unset($_SESSION[$authKey]);
        }
    } else {
        $_SESSION['no_adm_login_uid'] = $authAccount['uid'];
        $_SESSION['no_adm_login_uname'] = $authAccount['uname'];
        $_SESSION['no_adm_login_role_id'] = $authRoleId;
        $_SESSION['no_adm_login_role'] = $admin_roles[$authRoleId]['code'];
    }
}
$role = new Role();

if (empty($_SESSION['no_adm_login_uid'])) {
    $loginUrl = rtrim($GLOBALS['NO_ADMIN_BASE'] ?? '', '/') . '/';
    header('Location: ' . $loginUrl);
    exit;
}
