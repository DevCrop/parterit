<?php

final class AdminAjaxGuard
{
    public static function enforce(): void
    {
        include_once dirname(__DIR__, 2) . '/bootstrap/admin.php';

        if (empty($_SESSION['no_adm_login_uid'])) {
            echo json_encode([
                'result' => 'fail',
                'msg' => '로그인이 필요합니다. 다시 로그인해 주세요.',
            ]);
            exit;
        }
    }
}
