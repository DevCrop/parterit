<?php

final class LogoutHandler
{
    public static function handle(): void
    {
        include_once dirname(__DIR__, 2) . '/bootstrap/base.php';

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'] ?: '/',
                $params['domain'] ?? '',
                (bool) ($params['secure'] ?? false),
                (bool) ($params['httponly'] ?? false)
            );
        }

        setcookie('cookie_session_id', '', time() - 42000, '/');
        session_destroy();

        header('Location: ../../');
        exit;
    }
}
