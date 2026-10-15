<?php
$kind = ($kind ?? 'stories') === 'pricing' ? 'pricing' : 'stories';
$isPricingHero = $kind === 'pricing';
$heroLines = $isPricingHero ? ['기업의 성장 단계에 맞는', '플랜을 선택하세요'] : ['고객의 경험으로 만나는', '파트너잇'];
$descriptionLines = $isPricingHero
    ? ['무료 체험부터 협업, 사업 확장까지.', '기업에 필요한 기능과 계정 규모를 비교해 보세요.']
    : ['지원사업을 찾는 순간부터 AI 리포트를 확인하기까지.', '파트너잇을 먼저 경험한 고객들의 이야기를 만나보세요.'];
?>
<section class="partnerit-subhero partnerit-subhero--<?= e($kind) ?>" aria-labelledby="partnerit-subhero-title">
    <div class="partnerit-subhero__inner no-container-xl">
        <p class="partnerit-subhero__eyebrow f-body-3 --semibold"><?= $isPricingHero ? '요금정책' : 'CUSTOMER STORIES' ?></p>
        <h1 class="partnerit-subhero__title <?= $isPricingHero ? 'f-heading-pricing-hero' : 'f-heading-stories-hero' ?>" id="partnerit-subhero-title">
            <?php foreach ($heroLines as $line): ?><span><?= e($line) ?></span><?php endforeach; ?>
        </h1>
        <p class="partnerit-subhero__description <?= $isPricingHero ? 'f-body-pricing-lead' : 'f-body-stories-lead' ?>">
            <?php foreach ($descriptionLines as $line): ?><span><?= e($line) ?></span><?php endforeach; ?>
        </p>
    </div>
</section>
