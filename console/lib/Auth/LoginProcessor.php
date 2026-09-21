<?php

final class LoginProcessor
{
    public static function handle(): void
    {
        session_name('PARTNERITSESSID');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();

        require_once dirname(__DIR__, 2) . '/bootstrap/base.php';

        $pdo = DB::getInstance();

        $maxLoginFailures = 5;
        $loginBlockSeconds = 900;
        $loginAttemptDir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'nol_admin_login_attempts';
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $sendLoginError = static function (string $message): void {
            $_SESSION['admin_login_error'] = $message;
            $_SESSION['admin_login_uid'] = trim((string) ($_POST['uid'] ?? ''));
            header('Location: ../../index.php');
            exit;
        };

        $loadLoginAttempt = static function (string $path): array {
            if (!is_file($path)) {
                return ['failures' => 0, 'blocked_until' => 0];
            }

            $raw = @file_get_contents($path);
            if ($raw === false || $raw === '') {
                return ['failures' => 0, 'blocked_until' => 0];
            }

            $data = json_decode($raw, true);
            if (!is_array($data)) {
                return ['failures' => 0, 'blocked_until' => 0];
            }

            return [
                'failures' => (int) ($data['failures'] ?? 0),
                'blocked_until' => (int) ($data['blocked_until'] ?? 0),
            ];
        };

        $saveLoginAttempt = static function (string $dir, string $path, array $data): void {
            if (!is_dir($dir)) {
                @mkdir($dir, 0700, true);
            }

            @file_put_contents($path, json_encode([
                'failures' => (int) ($data['failures'] ?? 0),
                'blocked_until' => (int) ($data['blocked_until'] ?? 0),
            ], JSON_UNESCAPED_SLASHES), LOCK_EX);
        };

        $clearLoginAttempt = static function (string $path): void {
            if (is_file($path)) {
                @unlink($path);
            }
        };

        $uid = trim((string) ($_POST['uid'] ?? ''));
        $pwd = $_POST['upwd'] ?? '';
        $captcha = $_POST['r_captcha'] ?? '';
        $attemptKey = hash('sha256', strtolower($uid) . '|' . $clientIp);
        $attemptFile = $loginAttemptDir . DIRECTORY_SEPARATOR . $attemptKey . '.json';

        $recordLoginFailure = static function () use (
            $attemptFile,
            $loginAttemptDir,
            $loadLoginAttempt,
            $saveLoginAttempt,
            $maxLoginFailures,
            $loginBlockSeconds
        ): void {
            $state = $loadLoginAttempt($attemptFile);
            $state['failures']++;

            if ($state['failures'] >= $maxLoginFailures) {
                $state['blocked_until'] = time() + $loginBlockSeconds;
                $state['failures'] = 0;
            }

            $saveLoginAttempt($loginAttemptDir, $attemptFile, $state);
        };

        $attemptState = $loadLoginAttempt($attemptFile);
        if (($attemptState['blocked_until'] ?? 0) > time()) {
            $remainingMinutes = (int) ceil((($attemptState['blocked_until'] ?? 0) - time()) / 60);
            $sendLoginError("Too many login attempts. Try again in {$remainingMinutes} minute(s).");
        }

        if (($_SESSION['captcha_secure'] ?? '') !== $captcha) {
            $sendLoginError('보안코드가 일치하지 않습니다. 새로 표시된 보안코드를 입력해 주세요.');
        }

        $sql = "
            SELECT a.no, a.uid, a.upwd, a.uname, a.active_status, a.role_id
            FROM nb_admin a
            WHERE a.uid = :uid AND a.sitekey = :sitekey
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'uid' => $uid,
            'sitekey' => $NO_SITE_UNIQUE_KEY,
        ]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data || !password_verify($pwd, $data['upwd'])) {
            $recordLoginFailure();
            $sendLoginError('아이디 또는 비밀번호가 일치하지 않습니다.');
        }

        if (($data['active_status'] ?? '') === 'N') {
            $sendLoginError('사용 중지된 계정입니다.');
        }

        $clearLoginAttempt($attemptFile);

        global $admin_roles;

        $roleId = (int) ($data['role_id'] ?? 3);
        $roleCode = $admin_roles[$roleId]['code'] ?? 'guest';

        $_SESSION = [];
        session_regenerate_id(true);

        $_SESSION['no_adm_login_no'] = $data['no'];
        $_SESSION['no_adm_login_uid'] = $data['uid'];
        $_SESSION['no_adm_login_uname'] = $data['uname'];
        $_SESSION['no_adm_login_role_id'] = $roleId;
        $_SESSION['no_adm_login_role'] = $roleCode;

        header('Location: ../../pages/board/board.list.php');
        exit;
    }
}
