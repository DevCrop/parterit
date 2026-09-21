<?php
include_once dirname(__DIR__) . '/lib/admin.check.php';

// bootstrap globals 안전 기본값 — 직접 호출/세션 만료 시 Undefined Warning 차단
$NO_IS_SUBDIR  = $NO_IS_SUBDIR  ?? '';
$NO_ADMIN_BASE = $NO_ADMIN_BASE ?? '';

$roleTitle = '계정: ' . htmlspecialchars($NO_ADM_ID ?? '', ENT_QUOTES, 'UTF-8', false) . ' / 권한: ' . htmlspecialchars($role->getName(), ENT_QUOTES, 'UTF-8', false);
if ($role->isReadOnly()) {
    $roleTitle .= ' (조회 전용)';
}
?>

<header class="no-header" role="banner">
    <div class="no-header__inner">
        <div class="no-header__left">
            <div class="no-drawer-btn">
                <button type="button" aria-label="메뉴 열기" class="no-drawer-btn__trigger">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>

        <div class="no-header__right">
            <button type="button" class="no-header-menu-btn" aria-label="메뉴 열기" aria-expanded="false">
                <i class="bx bxs-user-circle" aria-hidden="true"></i>
            </button>
            <div class="no-nav-list">
                <nav class="no-header-actions" aria-label="상단 메뉴">
                    <ul class="no-header-actions__list">
                        <li class="no-header-actions__item">
                            <a href="<?= $NO_IS_SUBDIR ?>/index.php" target="_blank" rel="noopener" class="no-header-actions__link" title="사이트 새 창에서 보기">
                                <i class="bx bx-home-alt" aria-hidden="true"></i>
                                <span class="no-header-actions__text">홈페이지</span>
                            </a>
                        </li>
                        <?php if (!empty($_SESSION['no_adm_login_uid'])): ?>
                        <li class="no-header-actions__item">
                            <a href="<?= $NO_ADMIN_BASE ?>/lib/login/logout.php" class="no-header-actions__link no-header-actions__link--logout" title="로그아웃">
                                <i class="bx bx-log-out" aria-hidden="true"></i>
                                <span class="no-header-actions__text">로그아웃</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>

                <div class="no-header-user" title="<?= $roleTitle ?>">
                    <div class="no-header-user__avatar" aria-hidden="true">
                        <i class="bx bx-user"></i>
                    </div>
                    <div class="no-header-user__info">
                        <span class="no-header-user__name"><?= htmlspecialchars(($NO_ADM_NAME ?? '') ?: ($NO_ADM_ID ?? ''), ENT_QUOTES, 'UTF-8', false) ?></span>
                        <span class="no-header-user__meta">
                            <span class="no-header-user__role"><?= htmlspecialchars($role->getName(), ENT_QUOTES, 'UTF-8', false) ?></span>
                            <?php if ($role->isReadOnly()): ?>
                            <span class="no-header-user__badge no-header-user__badge--readonly" title="수정과 등록이 제한됩니다.">조회 전용</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <span class="no-header-user__status no-header-user__status--online" aria-hidden="true"></span>
                </div>
            </div>
        </div>
    </div>
</header>
