<?php

use Database\DB;

$conn = DB::getInstance();
$seoMeta = seo_manager()->buildForCurrentRequest();
$siteinfo = $seoMeta->siteInfo();
app()->share('siteinfo', $siteinfo);

$siteTagsEnabled = function_exists('project_feature_enabled')
    ? project_feature_enabled('content.site_tags', true)
    : true;
$alternateHreflangEnabled = !empty($seoMeta->alternateUrls());
?>

<!DOCTYPE html>
<html lang="<?= e($seoMeta->htmlLang()) ?>" data-base-path="<?= e(BASE_PATH) ?>">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" crossorigin
        href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css" />
    <!-- 폰트 단일화: Pretendard 만 로드 (Noto Serif KR · Space Grotesk · Noto Sans JP 제거) -->

    <?php
    $seo_title = yield_section('seo_title') ?: project_brand_name() . ' | 페이지 준비 중';
    $seo_desc = yield_section('seo_desc') ?: '';
    $seo_keywords = yield_section('seo_keywords') ?: '';
    ?>

    <title><?= e($seo_title) ?></title>
    <meta name="description" content="<?= e($seo_desc) ?>">
    <meta name="keywords" content="<?= e($seo_keywords) ?>">

    <link rel="canonical" href="<?= e($seoMeta->canonicalUrl()) ?>">
    <?php if ($alternateHreflangEnabled): ?>
    <?php foreach ($seoMeta->alternateUrls() as $loc => $href): ?>
    <link rel="alternate" hreflang="<?= e(LOCALES[$loc] ?? $loc) ?>" href="<?= e($href) ?>">
    <?php endforeach; ?>
    <?php if ($seoMeta->xDefaultUrl()): ?>
    <link rel="alternate" hreflang="x-default" href="<?= e($seoMeta->xDefaultUrl()) ?>">
    <?php endif; ?>
    <?php endif; ?>
    <meta name="robots" content="<?= e($seoMeta->robots()) ?>">
    <meta name="image" content="">

    <?php if ($seoMeta->naverVerification()): ?>
    <meta name="naver-site-verification" content="<?= e($seoMeta->naverVerification()) ?>">
    <?php endif; ?>
    <?php foreach ($seoMeta->googleVerifications() as $verification): ?>
    <meta name="google-site-verification" content="<?= e($verification) ?>">
    <?php endforeach; ?>

    <meta property="og:locale" content="<?= e($seoMeta->ogLocale()) ?>">
    <meta property="og:url" content="<?= e($seoMeta->canonicalUrl()) ?>">
    <?php $og_type = yield_section('og_type') ?: $seoMeta->ogType(); ?>
    <meta property="og:type" content="<?= e($og_type) ?>">
    <meta property="og:site_name" content="<?= e(project_brand_name()) ?>">
    <meta property="og:title" content="<?= e($seo_title) ?>">
    <meta property="og:description" content="<?= e($seo_desc) ?>">
    <meta property="og:image" content="<?= e(base_path('/resource/images/main/hero/laptop-composition.png')) ?>">

    <?php $twitter_card = yield_section('twitter_card') ?: $seoMeta->twitterCard(); ?>
    <meta name="twitter:card" content="<?= e($twitter_card) ?>">
    <meta name="twitter:title" content="<?= e($seo_title) ?>">
    <meta name="twitter:description" content="<?= e($seo_desc) ?>">
    <meta name="twitter:image" content="<?= e(base_path('/resource/images/main/hero/laptop-composition.png')) ?>">

    <link rel="icon" type="image/svg+xml" href="<?= e(base_path('/resource/images/logo/partnerit-favicon.svg')) ?>">
    <link rel="shortcut icon" href="<?= e(base_path('/resource/images/logo/partnerit-favicon.svg')) ?>">

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/intl-tel-input@26.8.1/build/css/intlTelInput.css">
    <link href="https://cdn.jsdelivr.net/npm/beerslider@1.0.3/dist/BeerSlider.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/resource/vendor/swiper/swiper-bundle.css">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/resource/vendor/aos/aos.css">
    <link rel="stylesheet" href="<?= base_path('/resource/dist/style.css', true) ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.1/themes/base/jquery-ui.min.css">
    <?= yield_section('style', '') ?>

    <?php
    $_profile      = project_profile();
    $_isHttpsForLd = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') ||
        (($_SERVER['SERVER_PORT'] ?? '') == 443)
    );
    $_baseUrlForLd = ($_isHttpsForLd ? 'https' : 'http')
                     . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    $_orgAddresses = [];
    foreach ($_profile['offices'] ?? [] as $_office) {
        if (!empty($_office['address'])) {
            $_orgAddresses[] = ['@type' => 'PostalAddress', 'streetAddress' => $_office['address']];
        }
    }
    $_contactPoints = [];
    foreach ($_profile['inquiries'] ?? [] as $_inq) {
        if (!empty($_inq['email'])) {
            $_contactPoints[] = ['@type' => 'ContactPoint', 'contactType' => $_inq['label'] ?? 'customer support', 'email' => $_inq['email']];
        }
    }
    $_sameAs = array_values(array_filter([
        $_profile['links']['linkedin']  ?? '',
        $_profile['links']['twitter']   ?? '',
        $_profile['links']['youtube']   ?? '',
        $_profile['links']['instagram'] ?? '',
    ]));
    $_orgLd = array_filter([
        '@context'     => 'https://schema.org',
        '@type'        => 'Organization',
        '@id'          => $_baseUrlForLd . '/#organization',
        'name'         => $_profile['branding']['display_name'] ?? '파트너잇',
        'url'          => $_baseUrlForLd . '/',
        'logo'         => !empty($_profile['assets']['logo_header_path']) ? ['@type' => 'ImageObject', 'url' => $_baseUrlForLd . $_profile['assets']['logo_header_path']] : null,
        'sameAs'       => $_sameAs ?: null,
        'address'      => $_orgAddresses ?: null,
        'contactPoint' => $_contactPoints ?: null,
    ], fn($v) => $v !== null && $v !== []);
    $_webLd = [
        '@context'  => 'https://schema.org',
        '@type'     => 'WebSite',
        '@id'       => $_baseUrlForLd . '/#website',
        'url'       => $_baseUrlForLd . '/',
        'name'      => $_profile['branding']['display_name'] ?? '파트너잇',
        'publisher' => ['@id' => $_baseUrlForLd . '/#organization'],
        'inLanguage' => ['ko-KR'],
    ];
    ?>
    <script type="application/ld+json"><?= json_encode($_orgLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
    <script type="application/ld+json"><?= json_encode($_webLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
    <?= yield_section('json_ld', '') ?>

    <script src="<?= BASE_PATH ?>/resource/vendor/jquery/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.1/jquery-ui.min.js"></script>
    <script src="<?= BASE_PATH ?>/resource/vendor/swiper/swiper-bundle.js"></script>
    <script src="<?= BASE_PATH ?>/resource/vendor/aos/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/beerslider@1.0.3/dist/BeerSlider.min.js"></script>
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="<?= BASE_PATH ?>/resource/vendor/fontAwesome/fontAwesome.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/ScrollTrigger.min.js"></script>

    <script>
    document.documentElement.classList.add("js");
    </script>

    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@26.8.1/build/js/intlTelInput.min.js" defer></script>
    <script src="<?= base_path('/resource/dist/app.js', true) ?>" defer></script>

    <?php
    try {
        if ($siteTagsEnabled) {
            $tagStmt = $conn->prepare('SELECT tag_content FROM nb_site_tags WHERE location = 1 AND is_active = 1 ORDER BY id ASC');
            $tagStmt->execute();
            $headTags = $tagStmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($headTags as $tag) {
                echo sanitize_embed_html($tag['tag_content'] ?? '') . "\n    ";
            }
        }
    } catch (Exception $e) {
    }
    ?>
</head>

<body>
    <?php
    try {
        if ($siteTagsEnabled) {
            $tagStmt = $conn->prepare('SELECT tag_content FROM nb_site_tags WHERE location = 2 AND is_active = 1 ORDER BY id ASC');
            $tagStmt->execute();
            $bodyTags = $tagStmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($bodyTags as $tag) {
                echo sanitize_embed_html($tag['tag_content'] ?? '') . "\n    ";
            }
        }
    } catch (Exception $e) {
    }
    ?>
    <div class="__root">
        <a href="#main-content" class="sr-only sr-only--focusable"><?= locale() === 'ko' ? '본문 바로가기' : 'Skip to main content' ?></a>
        <?php
        $isRoot = current_route_is('home');
        $mainClass = yield_section('main_class');
        if ($isRoot) {
            $mainClass .= $mainClass ? ' no-main' : 'no-main';
        } else {
            $mainClass .= $mainClass ? ' no-sub' : 'no-sub';
        }
        $mainClass = trim($mainClass);
        ?>
        <?= include_view('components.header') ?>
        <?= include_view('components.backdrop') ?>
        <?= include_view('components.drawer') ?>
        <?= include_view('components.floating-button') ?>

        <main id="main-content" class="<?= $mainClass ?>">
            <?= yield_section('content') ?>
        </main>
        <?= include_view('components.footer') ?>
    </div>

    <?= yield_section('portal', '') ?>
    <?= include_view('components.partnerit-brochure-modal') ?>
    <?= include_view('components.popup') ?>
    <?= yield_section('script', '') ?>
</body>

</html>
