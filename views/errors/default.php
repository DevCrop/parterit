<?php
/** @var int $status */
/** @var string $message */
/** @var array|null $debug */
/** @var array<string>|null $allowed */
/** @var string $path */
/** @var string $method */

$h = static fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$siteinfo = [];
if (function_exists('app')) {
    try {
        $siteinfoValue = app()->get('siteinfo');
        if (is_array($siteinfoValue)) {
            $siteinfo = $siteinfoValue;
        }
    } catch (\Throwable $e) {
        $siteinfo = [];
    }
}
$basePath = defined('BASE_PATH') ? (string) BASE_PATH : '';
$siteName = trim((string) ($siteinfo['title'] ?? ''));
if ($siteName === '') {
    $siteName = function_exists('project_brand_name') ? project_brand_name() : 'Site';
}
$siteFooterName = trim((string) ($siteinfo['footer_name'] ?? ''));
if ($siteFooterName === '') {
    $siteFooterName = $siteName;
}
$locale = function_exists('locale') ? (string) locale() : 'ko';
$code = $status ?? 500;
$msg = $message ?? 'Something went wrong';
$is405 = ($code === 405);
$is404 = ($code === 404);
$is500 = ($code >= 500);
$homeUrl = function_exists('localized_path')
    ? localized_path('/')
    : (($basePath ?: '') . '/');
$meetingUrl = function_exists('project_primary_cta_url')
    ? project_primary_cta_url()
    : (($basePath ?: '') . '/contact');
?>
<!DOCTYPE html>
<html lang="<?= $h($locale) ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $h($code) ?> · <?= $h($msg) ?> | <?= $h($siteName) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <?php
    $errThemeHref = function_exists('base_path')
        ? base_path('/resource/dist/style.css')
        : (($basePath !== '' ? rtrim($basePath, '/') : '') . '/resource/dist/style.css');
    ?>
    <link rel="stylesheet" href="<?= $h($errThemeHref) ?>">

    <style>
    :root {
        --no-bg: var(--clr-ui-bg, #fff);
        --no-fg: var(--clr-text-title, #111827);
        --no-fg-muted: var(--clr-gray-500, #6b7280);
        --no-border: var(--clr-gray-200, #e5e7eb);
        --no-accent: var(--clr-primary-def, #226d52);
        --no-accent-weak: var(--clr-primary-50, #f0f9f4);
        --no-accent-strong: var(--clr-primary-900, #163e31);
        --no-badge-bg: var(--clr-primary-50, #f0f9f4);
        --no-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    @media (prefers-color-scheme: dark) {
        :root {
            --no-bg: #121212;
            --no-fg: #f9fafb;
            --no-fg-muted: var(--clr-gray-400, #9ca3af);
            --no-border: var(--clr-gray-800, #1f2937);
            --no-accent: var(--clr-primary-300, #89ccae);
            --no-accent-weak: color-mix(in srgb, var(--clr-primary-def, #226d52) 14%, #121212);
            --no-accent-strong: var(--clr-primary-100, #daf1e4);
            --no-badge-bg: color-mix(in srgb, var(--clr-primary-def, #226d52) 12%, #121212);
            --no-shadow: 0 10px 30px rgba(0, 0, 0, 0.45);
        }
    }

    * { box-sizing: border-box; }
    html, body { height: 100%; }

    .no-error {
        min-height: 100dvh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 48px 16px;
    }

    .no-error__container {
        width: min(920px, 100%);
        background: var(--no-bg);
        border: 1px solid var(--no-border);
        border-radius: 20px;
        box-shadow: var(--no-shadow);
        overflow: hidden;
    }

    .no-error__header {
        display: flex;
        gap: 16px;
        padding: 28px 28px 8px;
        align-items: flex-start;
    }

    .no-error__badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-radius: 999px;
        background: var(--no-badge-bg);
        border: 1px solid var(--no-border);
        font-weight: 600;
        letter-spacing: .2px;
        color: var(--no-accent-strong);
    }

    .no-error__badge svg {
        width: 20px;
        height: 20px;
        flex: 0 0 20px;
    }

    .no-error__code {
        font-size: 13px;
        font-weight: 700;
        color: var(--no-accent-strong);
    }

    .no-error__title {
        margin: 0;
        padding: 0 28px 8px;
        font-size: clamp(22px, 3.2vw, 32px);
        line-height: 1.2;
        font-weight: 800;
    }

    .no-error__desc {
        margin: 0;
        padding: 0 28px 18px;
        font-size: 16px;
        color: var(--no-fg-muted);
    }

    .no-error__panel {
        display: grid;
        grid-template-columns: 1.1fr .9fr;
        gap: 20px;
        padding: 0 28px 24px;
    }

    @media (max-width: 760px) {
        .no-error__panel { grid-template-columns: 1fr; }
    }

    .no-error__card {
        border: 1px solid var(--no-border);
        border-radius: 16px;
        padding: 18px;
        background: linear-gradient(
            180deg,
            color-mix(in srgb, var(--no-accent) 10%, transparent) 0%,
            transparent 100%
        );
    }

    .no-error__meta {
        display: grid;
        grid-template-columns: 120px 1fr;
        row-gap: 10px;
        column-gap: 12px;
        font-size: 14px;
    }

    .no-error__meta dt { color: var(--no-fg-muted); }
    .no-error__meta dd { margin: 0; word-break: break-all; }

    .no-error__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 0 28px 28px;
    }

    .no-btn {
        appearance: none;
        border: 1px solid var(--no-border);
        background: var(--no-bg);
        color: var(--no-fg);
        padding: 12px 16px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: transform .02s ease, background .2s ease, border-color .2s ease;
    }

    .no-btn:active { transform: translateY(1px); }
    .no-btn--primary { background: var(--no-accent); color: #fff; border-color: transparent; }
    .no-btn--ghost { background: transparent; }
    .no-btn--link { background: transparent; border-color: transparent; color: var(--no-accent-strong); text-decoration: underline; }

    .no-error__hint {
        padding: 0 28px 24px;
        color: var(--no-fg-muted);
        font-size: 13px;
    }

    .no-error__debug { padding: 0 28px 28px; }

    .no-error__details {
        border: 1px dashed var(--no-border);
        border-radius: 14px;
        padding: 14px 16px;
        background: rgba(0, 0, 0, 0.02);
    }

    @media (prefers-color-scheme: dark) {
        .no-error__details { background: rgba(255, 255, 255, 0.03); }
    }

    .no-error__details summary {
        cursor: pointer;
        font-weight: 700;
        color: var(--no-accent-strong);
        margin-bottom: 8px;
    }

    .no-error__pre {
        margin: 10px 0 0;
        max-height: 360px;
        overflow: auto;
        font-size: 12px;
        line-height: 1.45;
        background: var(--clr-primary-950, #0b231c);
        color: var(--clr-primary-100, #daf1e4);
        padding: 12px 14px;
        border-radius: 10px;
    }

    .no-error__foot {
        padding: 18px 28px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        border-top: 1px solid var(--no-border);
        font-size: 12px;
        color: var(--no-fg-muted);
    }

    .no-error__brand {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
        letter-spacing: .2px;
    }

    .no-error__brand-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--no-accent);
        box-shadow: 0 0 0 4px color-mix(in srgb, var(--no-accent) 18%, transparent);
    }
    </style>
</head>

<body>
    <main class="no-error" role="main">
        <section class="no-error__container" aria-labelledby="error-title">
            <header class="no-error__header">
                <div class="no-error__badge" aria-label="<?= $h($siteName) ?> <?= locale() === 'ko' ? '오류 안내' : 'Error Notice' ?>">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 2c3 2 6 2.5 9 3v5.5c0 4.9-3.2 9.3-9 11-5.8-1.7-9-6.1-9-11V5c3-.5 6-1 9-3Z"
                            stroke="currentColor" stroke-width="1.2" />
                        <path d="M15.5 8.5c-3.2-.6-5.7 .9-7.5 4 2.9.5 5.2-.6 7.5-4Z" fill="currentColor" opacity=".15" />
                        <path d="M8 13c1.8-3.1 4.3-4.6 7.5-4M8 13c1.7.3 3.5.1 5.5-.8" stroke="currentColor"
                            stroke-width="1.2" stroke-linecap="round" />
                    </svg>
                    <span class="no-error__code"><?= $h($code) ?></span>
                </div>
            </header>

            <h1 id="error-title" class="no-error__title">
                <?php if ($is404): ?>
                <?= locale() === 'ko' ? '요청하신 페이지를 찾을 수 없습니다' : 'The page you requested could not be found' ?>
                <?php elseif ($is405): ?>
                <?= locale() === 'ko' ? '요청하신 방식은 지원되지 않습니다' : 'The request method is not supported' ?>
                <?php elseif ($is500): ?>
                <?= locale() === 'ko' ? '일시적인 문제가 발생했습니다' : 'A temporary error has occurred' ?>
                <?php else: ?>
                <?= locale() === 'ko' ? '예기치 않은 오류가 발생했습니다' : 'An unexpected error occurred' ?>
                <?php endif; ?>
            </h1>

            <p class="no-error__desc">
                <?= $h($msg) ?> · <strong><?= $h($method) ?></strong> <code><?= $h($path) ?></code>
                <?php if ($is405 && !empty($allowed)): ?>
                · <?= locale() === 'ko' ? '허용 메서드' : 'Allowed methods' ?> <?= $h(implode(', ', array_map('strtoupper', (array) $allowed))) ?>
                <?php endif; ?>
            </p>

            <div class="no-error__panel">
                <div class="no-error__card">
                    <dl class="no-error__meta">
                        <dt><?= locale() === 'ko' ? '상태 코드' : 'Status Code' ?></dt>
                        <dd><?= $h($code) ?></dd>
                        <dt><?= locale() === 'ko' ? '요청 경로' : 'Request Path' ?></dt>
                        <dd><code><?= $h($path) ?></code></dd>
                        <dt><?= locale() === 'ko' ? '요청 방식' : 'Request Method' ?></dt>
                        <dd><?= $h($method) ?></dd>
                        <dt><?= locale() === 'ko' ? '안내' : 'Note' ?></dt>
                        <dd>
                            <?php if ($is404): ?>
                            <?= locale() === 'ko' ? '주소가 정확한지 확인해주세요. 메뉴를 통해 다시 이동하실 수 있습니다.' : 'Check the URL and try again, or navigate using the menu.' ?>
                            <?php elseif ($is405): ?>
                            <?= locale() === 'ko' ? '해당 경로는 다른 요청 방식으로만 접근할 수 있습니다.' : 'This path only accepts requests via a different method.' ?>
                            <?php elseif ($is500): ?>
                            <?= locale() === 'ko' ? '잠시 후 다시 시도해주세요. 문제가 반복되면 문의 부탁드립니다.' : 'Please try again shortly. If the issue persists, contact us.' ?>
                            <?php else: ?>
                            <?= locale() === 'ko' ? '문제가 계속되면 문의 채널을 통해 알려주세요.' : 'If the problem continues, let us know through our contact channel.' ?>
                            <?php endif; ?>
                        </dd>
                    </dl>
                </div>

                <div class="no-error__card">
                    <div style="font-weight:700; margin-bottom:10px;"><?= locale() === 'ko' ? '빠른 이동' : 'Quick Links' ?></div>
                    <div class="no-error__actions">
                        <a class="no-btn no-btn--primary" href="<?= $h($homeUrl) ?>"><?= locale() === 'ko' ? '홈으로' : 'Home' ?></a>
                        <button class="no-btn no-btn--ghost" onclick="history.back()"><?= locale() === 'ko' ? '이전 페이지' : 'Go Back' ?></button>
                        <button class="no-btn no-btn--ghost" onclick="location.reload()"><?= locale() === 'ko' ? '새로고침' : 'Refresh' ?></button>
                        <a class="no-btn no-btn--link" href="<?= $h($meetingUrl) ?>"><?= locale() === 'ko' ? '문의하기' : 'Contact Us' ?></a>
                    </div>
                    <p class="no-error__hint"><?= locale() === 'ko' ? '문제가 반복되면 발생 시점과 화면 상태를 함께 전달해주시면 더 빠르게 확인할 수 있습니다.' : 'If the issue recurs, share when it happened and what the screen showed — it helps us diagnose faster.' ?></p>
                </div>
            </div>

            <?php if (!empty($debug)): ?>
            <div class="no-error__debug">
                <details class="no-error__details">
                    <summary><?= locale() === 'ko' ? '개발자 정보' : 'Developer Info' ?> (<?= $h($debug['file'] ?? '') ?>:<?= $h($debug['line'] ?? '') ?>)</summary>
                    <?php if (!empty($debug['message'])): ?>
                    <div><strong>Message:</strong> <?= $h($debug['message']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($debug['trace']) && is_array($debug['trace'])): ?>
                    <pre class="no-error__pre"><?= $h(implode("\n", $debug['trace'])) ?></pre>
                    <?php endif; ?>
                </details>
            </div>
            <?php endif; ?>

            <footer class="no-error__foot">
                <span><?= locale() === 'ko' ? '요청:' : 'Request:' ?> <code><?= $h($method) ?></code> <code><?= $h($path) ?></code></span>
                <span class="no-error__brand"><span class="no-error__brand-dot"></span> <?= $h($siteFooterName) ?></span>
            </footer>
        </section>
    </main>
</body>

</html>
