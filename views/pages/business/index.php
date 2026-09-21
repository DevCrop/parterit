<?php
$isConsortiumMatching = current_route_is('business.consortium-matching');
$isAnnouncementMatching = current_route_is('business.announcement-matching');
$isAiReport = current_route_is('business.ai-report');
$isPricing = current_route_is('pricing');
$seoTitle = $isConsortiumMatching ? '컨소시엄 매칭 | 파트너잇' : ($isAnnouncementMatching ? '공고 매칭 | 파트너잇' : '파트너잇');
$seoDescription = $isConsortiumMatching
    ? '기업의 기술과 사업 역량을 분석해 공동사업에 적합한 협력기업을 연결하는 파트너잇 컨소시엄 매칭 서비스입니다.'
    : ($isAnnouncementMatching ? '기업 프로필과 공고 조건을 분석해 실제 지원 가능한 사업을 추천하는 파트너잇 공고 매칭 서비스입니다.' : '');
?>
<?php
if ($isAiReport) {
    $seoTitle = 'AI 보고서 | 파트너잇';
    $seoDescription = '지원자격, 우대조건, 가점요소와 기업 역량을 함께 분석해 지원 판단을 돕는 파트너잇 AI 보고서입니다.';
}
if ($isPricing) {
    $seoTitle = '요금정책 | 파트너잇';
    $seoDescription = '무료 체험부터 협업, 사업 확장까지. 기업에 필요한 파트너잇 기능과 계정 규모를 비교해 보세요.';
}
?>
<?php section('seo_title', $seoTitle) ?>
<?php section('seo_desc', $seoDescription) ?>
<?php section('content') ?>
<?php if ($isConsortiumMatching): ?>
    <?= include_view('components.partnerit-consortium-hero') ?>
    <?= include_view('components.partnerit-consortium-problem') ?>
    <?= include_view('components.partnerit-consortium-matching-system') ?>
    <?= include_view('components.partnerit-consortium-features') ?>
    <?= include_view('components.partnerit-consortium-collaboration') ?>
    <?= include_view('components.partnerit-reviews') ?>
    <?= include_view('components.partnerit-partners') ?>
    <?= include_view('components.partnerit-final-cta') ?>
<?php elseif ($isAnnouncementMatching): ?>
    <div class="partnerit-announcement">
    <?= include_view('components.partnerit-announcement-hero') ?>
    <?= include_view('components.partnerit-announcement-problem') ?>
    <?= include_view('components.partnerit-announcement-collection') ?>
    <?= include_view('components.partnerit-announcement-profile') ?>
    <?= include_view('components.partnerit-announcement-selection') ?>
    <?= include_view('components.partnerit-announcement-management') ?>
    <?= include_view('components.partnerit-announcement-report') ?>
    </div>
    <?= include_view('components.partnerit-reviews') ?>
    <?= include_view('components.partnerit-partners') ?>
    <?= include_view('components.partnerit-final-cta') ?>
<?php elseif ($isAiReport): ?>
    <div class="partnerit-ai-report">
        <?= include_view('components.partnerit-ai-report-hero') ?>
        <?= include_view('components.partnerit-ai-report-context') ?>
        <?= include_view('components.partnerit-ai-report-preparation') ?>
        <?= include_view('components.partnerit-ai-report-diagnosis') ?>
        <?= include_view('components.partnerit-ai-report-capability') ?>
    </div>
    <?= include_view('components.partnerit-reviews') ?>
    <?= include_view('components.partnerit-partners') ?>
    <?= include_view('components.partnerit-final-cta') ?>
<?php elseif ($isPricing): ?>
    <div class="partnerit-pricing-page">
        <?= include_view('components.partnerit-subhero', ['kind' => 'pricing']) ?>
        <?= include_view('components.partnerit-pricing') ?>
    </div>
    <?= include_view('components.partnerit-reviews') ?>
    <?= include_view('components.partnerit-partners') ?>
    <?= include_view('components.partnerit-final-cta') ?>
<?php endif; ?>
<?php end_section() ?>
