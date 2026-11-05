<?php
$siteTagsEnabled = function_exists('project_feature_enabled')
    ? project_feature_enabled('content.site_tags', true)
    : true;

$footerNav = [
    ['label' => '회사소개', 'href' => base_path('/about')],
    ['label' => '공고매칭', 'href' => base_path('/matching')],
    ['label' => 'AI 보고서', 'href' => base_path('/ai-report')],
    ['label' => '컨소시엄 매칭', 'href' => base_path('/consortium')],
    ['label' => '요금정책', 'href' => base_path('/pricing')],
    ['label' => '고객사례', 'href' => base_path('/#partnerit-reviews-title')],
];

// ViewEngine includes components in method scope; site.info.php exports globals.
$footerValue = static function (string $key, string $fallback): string {
    return array_key_exists($key, $GLOBALS) ? trim((string) $GLOBALS[$key]) : $fallback;
};
$footerCompany = $footerValue('SITEINFO_FOOTER_NAME', '주식회사 파트너잇');
$footerOwner = $footerValue('SITEINFO_FOOTER_OWNER', '김남훈');
$footerBusinessNo = $footerValue('SITEINFO_FOOTER_SSN', '865-87-03332');
$footerAddress = $footerValue('SITEINFO_FOOTER_ADDRESS', '부산광역시 해운대구 센텀중앙로 90, 큐비e센텀 917호');
$footerPhone = $footerValue('SITEINFO_FOOTER_PHONE', '010-9235-4407');
$footerEmail = $footerValue('SITEINFO_FOOTER_EMAIL', 'partnerit917@gmail.com');
$footerPhoneHref = preg_replace('/[^0-9+]/', '', $footerPhone);
$footerEmailHref = filter_var($footerEmail, FILTER_VALIDATE_EMAIL) ? 'mailto:' . $footerEmail : '';
?>
<footer id="main-footer" class="no-footer">
    <div class="no-footer__inner no-container-xl">
        <div class="no-footer__main">
            <div class="no-footer__brand">
                <a href="<?= e(base_path('/')) ?>" class="no-footer__logo" aria-label="파트너잇 홈">
                    <?= image_picture('main/footer/partnerit-white.svg', [
                        'alt' => 'PARTNER IT',
                        'class' => 'no-footer__logo-image',
                    ]) ?>
                </a>
                <p class="no-footer__tagline f-body-footer-nav">
                    함께하는 성장의 연결고리, 기회와 파트너를 잇:다
                </p>
                <address class="no-footer__company f-body-footer-info">
                    <?php if ($footerCompany !== '' || $footerOwner !== ''): ?>
                        <span><?= e($footerCompany) ?><?php if ($footerCompany !== '' && $footerOwner !== ''): ?>&nbsp; | &nbsp;<?php endif ?><?php if ($footerOwner !== ''): ?>대표 <?= e($footerOwner) ?><?php endif ?></span>
                    <?php endif ?>
                    <?php if ($footerBusinessNo !== ''): ?><span>사업자등록번호 <?= e($footerBusinessNo) ?></span><?php endif ?>
                    <?php if ($footerAddress !== ''): ?><span><?= e($footerAddress) ?></span><?php endif ?>
                    <?php if ($footerPhone !== '' || $footerEmail !== ''): ?>
                        <span><?php if ($footerPhoneHref !== ''): ?><a href="<?= e('tel:' . $footerPhoneHref) ?>"><?= e($footerPhone) ?></a><?php else: ?><?= e($footerPhone) ?><?php endif ?><?php if ($footerPhone !== '' && $footerEmail !== ''): ?>&nbsp; | &nbsp;<?php endif ?><?php if ($footerEmailHref !== ''): ?><a href="<?= e($footerEmailHref) ?>"><?= e($footerEmail) ?></a><?php else: ?><?= e($footerEmail) ?><?php endif ?></span>
                    <?php endif ?>
                </address>
            </div>

            <nav class="no-footer__nav" aria-label="푸터 메뉴">
                <ul class="no-footer__menu">
                    <?php foreach ($footerNav as $item): ?>
                        <li class="no-footer__menu-item">
                            <a href="<?= e($item['href']) ?>" class="no-footer__menu-link f-body-footer-nav">
                                <span><?= e($item['label']) ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>

        <div class="no-footer__bottom">
            <p class="no-footer__copyright f-body-micro --medium">
                © <?= date('Y') ?> PARTNERIT. All Rights Reserved.
            </p>
            <ul class="no-footer__policies">
                <li>
                    <a href="<?= e(route('legal.privacy')) ?>" class="no-footer__policy f-body-micro --medium">개인정보처리방침</a>
                </li>
                <li>
                    <a href="<?= e(route('legal.cookies')) ?>" class="no-footer__policy f-body-micro --medium">이용약관</a>
                </li>
            </ul>
        </div>
    </div>
</footer>

<?php
try {
    if ($siteTagsEnabled) {
        $conn = DB::getInstance();
        $tagStmt = $conn->prepare("SELECT tag_content FROM nb_site_tags WHERE location = 3 AND is_active = 1 ORDER BY id ASC");
        $tagStmt->execute();
        $footerTags = $tagStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($footerTags as $tag) {
            echo $tag['tag_content'] . "\n";
        }
    }
} catch (Exception $e) {
}
?>
