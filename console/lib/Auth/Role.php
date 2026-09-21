<?php

/**
 * 관리자 권한 모델.
 *
 * 전역 $admin_roles 설정과 현재 세션을 기반으로
 * 콘솔 접근 권한과 메뉴 제약을 판단한다.
 */
class Role
{
    private string $code;
    private int $id;

    public function __construct(?int $roleId = null)
    {
        global $admin_roles;

        $this->id = $roleId ?? ($_SESSION['no_adm_login_role_id'] ?? 3);
        $this->code = $admin_roles[$this->id]['code'] ?? 'guest';
    }

    public function is(string $roleCode): bool
    {
        return $this->code === $roleCode;
    }

    public function in(array $allowedCodes): bool
    {
        return in_array($this->code, $allowedCodes, true);
    }

    public function canEdit(): bool
    {
        return $this->is('superadmin');
    }

    public function canDelete(): bool
    {
        return $this->in(['superadmin', 'manager']);
    }

    public function canView(): bool
    {
        return $this->is('superadmin');
    }

    public function canViewMenu(string $menuKey): bool
    {
        if ($this->is('external')) {
            return in_array($menuKey, ['request', 'siteinfo'], true);
        }

        return true;
    }

    public function getName(): string
    {
        global $admin_roles;

        return $admin_roles[$this->id]['name'] ?? '권한 없음';
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public static function getRoleLimit(int $roleId): ?int
    {
        $limits = [1 => 3, 2 => 10];

        return $limits[$roleId] ?? null;
    }

    public function getRestrictedMenuKeys(): array
    {
        $restricted = [];

        if ($this->is('external')) {
            $restricted = array_merge($restricted, ['account', 'siteinfo']);
        }

        if ($this->is('manager')) {
            $restricted = array_merge($restricted, ['account', 'user']);
        }

        return array_unique($restricted);
    }

    public function isReadOnly(): bool
    {
        return $this->is('external');
    }

    public function requireLogin(): void
    {
        if (!empty($_SESSION['no_adm_login_uid'])) {
            return;
        }

        $this->send403Json('로그인이 필요합니다.');
    }

    public function requireCanModify(): void
    {
        if (!$this->isReadOnly()) {
            return;
        }

        $this->send403Json('권한이 없습니다.');
    }

    private function send403Json(string $message): void
    {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(403);
        }

        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }

    public function redirectIfCannotView(?string $backUrl = null): void
    {
        if ($this->canView()) {
            return;
        }

        $this->redirectWithError('권한이 없습니다.', $backUrl);
    }

    public function redirectIfRestrictedMenu(string $menuKey): void
    {
        if (!in_array($menuKey, $this->getRestrictedMenuKeys(), true)) {
            return;
        }

        $this->redirectWithError('권한이 없습니다.');
    }

    public function redirectWithError(string $message, ?string $backUrl = null): void
    {
        $_SESSION['admin_flash_error'] = $message;
        $pagesBase = $GLOBALS['NO_ADMIN_PAGES_BASE'] ?? '';
        if ($pagesBase === '') {
            $pagesBase = no_admin_pages_base_from_script();
        }
        if ($pagesBase === '') {
            $adminBase = $GLOBALS['NO_ADMIN_BASE'] ?? '';
            if ($adminBase !== '') {
                $pagesBase = rtrim($adminBase, '/') . '/pages';
            }
        }

        $defaultBack = rtrim($pagesBase, '/') . '/board/board.list.php';
        $back = $this->resolveSafeRedirectUrl($backUrl, $defaultBack);

        header('Location: ' . $back);
        exit;
    }

    private function resolveSafeRedirectUrl(?string $preferUrl, string $default): string
    {
        $base = $GLOBALS['NO_IS_SUBDIR'] ?? '';

        if ($preferUrl !== null && $preferUrl !== '') {
            if (
                strpos($preferUrl, '//') === false
                && strpos($preferUrl, '..') === false
                && preg_match('#^/[^\\\\]*$#', $preferUrl)
            ) {
                return $base . $preferUrl;
            }

            return $default;
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if ($referer === '') {
            return $default;
        }

        $refererHost = parse_url($referer, PHP_URL_HOST);
        $refererScheme = parse_url($referer, PHP_URL_SCHEME);
        $currentHost = $_SERVER['HTTP_HOST'] ?? '';
        if (strpos($currentHost, ':') !== false) {
            $currentHost = explode(':', $currentHost, 2)[0];
        }

        $refererHost = $refererHost ? strtolower($refererHost) : '';
        $currentHost = strtolower($currentHost);

        if ($refererScheme !== 'http' && $refererScheme !== 'https') {
            return $default;
        }

        if ($refererHost !== '' && $refererHost === $currentHost) {
            return $referer;
        }

        return $default;
    }

    public function redirectIfReadOnly(?string $backUrl = null): void
    {
        if (!$this->isReadOnly()) {
            return;
        }

        $this->redirectWithError('권한이 없습니다.', $backUrl);
    }

    public function echoFlashErrorAlert(): void
    {
        $message = $_SESSION['admin_flash_error'] ?? '';
        if ($message !== '') {
            unset($_SESSION['admin_flash_error']);
        }
        if ($message === '') {
            return;
        }

        $messageEscaped = json_encode($message, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        echo '<script>';
        echo '(function(){ var m = ' . $messageEscaped . '; if (typeof window !== "undefined" && m) window.alert(m); })();';
        echo '</script>';
    }
}
