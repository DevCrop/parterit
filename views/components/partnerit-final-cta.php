<?php
$ctaHeading = ['우리 기업에 맞는 기회를', '파트너잇에서 시작해 보세요.'];
$ctaDescription = '공고를 찾는 시간은 줄이고, 더 좋은 기회와 파트너에 집중하세요.';
if (current_route_is('business.announcement-matching')) {
    $ctaHeading = ['우리 기업에 맞는 지원사업,', '파트너잇에서 확인해 보세요.'];
    $ctaDescription = '기업정보를 기반으로 추천된 공고부터 지원 조건과 마감일까지 확인하세요.';
} elseif (current_route_is('business.ai-report')) {
    $ctaHeading = ['복잡한 공고,', '지원 판단에 필요한 정보부터.'];
    $ctaDescription = '자격요건과 가점, 준비사항을 AI 보고서로 확인해 보세요.';
} elseif (current_route_is('business.consortium-matching')) {
    $ctaHeading = ['함께할 파트너를 찾고,', '다음 사업기회로 연결하세요.'];
    $ctaDescription = '우리 기업의 기술과 역량을 바탕으로 협력기업과 참여 가능한 컨소시엄을 확인하세요.';
}
?>
<section class="partnerit-final-cta" aria-labelledby="partnerit-final-cta-title">
    <div class="partnerit-final-cta__background" aria-hidden="true">
        <?= image_picture('main/footer/final-cta-background-260916.png', [
            'alt' => '',
            'loading' => 'lazy',
            'decoding' => 'async',
            'class' => 'partnerit-final-cta__background-image',
        ]) ?>
    </div>

    <div class="partnerit-final-cta__inner no-container-xl">
        <div class="partnerit-final-cta__copy">
            <div class="partnerit-final-cta__heading">
                <h2 class="partnerit-final-cta__title f-heading-1 --bold" id="partnerit-final-cta-title">
                    <?php foreach ($ctaHeading as $line): ?><span><?= e($line) ?></span><?php endforeach; ?>
                </h2>
                <p class="partnerit-final-cta__description f-body-3">
                    <?= e($ctaDescription) ?>
                </p>
            </div>

            <div class="partnerit-final-cta__actions">
                <?= include_view('components.partnerit-cta-link', [
                    'href' => 'https://app.partnerit.kr/account/signin',
                    'newTab' => true,
                    'label' => '무료로 체험하기',
                    'variant' => 'primary',
                ]) ?>
                <?= include_view('components.partnerit-cta-link', [
                    'href' => base_path('/matching'),
                    'label' => '서비스 소개서 다운로드',
                    'variant' => 'secondary',
                ]) ?>
            </div>
        </div>
    </div>
</section>
