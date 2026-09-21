<?php

final class AdminPageGuard
{
    public static function enforce(): void
    {
        include_once dirname(__DIR__, 2) . '/bootstrap/admin.php';

        if (empty($_SESSION['no_adm_login_uid'])) {
            alert('로그인이 필요합니다.', $GLOBALS['NO_ADMIN_BASE'] ?? '/');
            exit;
        }
    }
}
