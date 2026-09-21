<?php
$plans = [
    [
        'name' => 'Starter',
        'tone' => 'starter',
        'headline' => ['처음 시작하는 기업을 위한', '무료 체험 플랜'],
        'price' => 'FREE',
        'features' => ['AI 맞춤 공고 매칭 (1일 5건)', '기본 공고 탐색', '관심 공고 저장'],
        'scale' => '계정 제한 최대 1인 (대표자 계정)',
        'perks' => ['맞춤 공고'],
    ],
    [
        'name' => 'Growth',
        'tone' => 'growth',
        'headline' => ['성장하는 기업을 위한', '협업 성장 플랜'],
        'price' => '99,000원',
        'price_suffix' => '/ 월',
        'features' => ['AI 맞춤 공고 매칭 (무제한)', '파트너 자동 매칭', '실시간 공고 알림', 'AI 분석 리포트'],
        'scale' => '계정 제한 최대 3인 (권한 부여)',
        'perks' => ['맞춤 공고', '파트너 매칭', 'AI 리포트', '추천 알림', 'AI 파트너'],
    ],
    [
        'name' => 'Pro',
        'tone' => 'pro',
        'headline' => ['수주부터 투자, 사업 확장까지 연결하는', '사업 확장 플랜'],
        'price' => '159,000원',
        'price_suffix' => '/ 월',
        'features' => ['협업툴 (컨소시엄 공통 작업 포함)', '데이터 분석 대시보드', '관리자 모드 제공 및 팀원 규모 확장', '그로스 플랜의 모든 기능'],
        'scale' => '계정 제한 최대 10인 (최대 규모)',
        'perks' => ['모든 기능', '프로젝트 관리', '클라우드', '화상 회의'],
    ],
];

$perkIcons = [
    '맞춤 공고' => 'perk-notice', '파트너 매칭' => 'perk-match',
    'AI 리포트' => 'perk-report', '추천 알림' => 'perk-alert',
    'AI 파트너' => 'perk-partner', '모든 기능' => 'perk-all',
    '프로젝트 관리' => 'perk-project', '클라우드' => 'perk-cloud',
    '화상 회의' => 'perk-video',
];

$addons = [
    ['title' => '컨시어지 서비스', 'description' => '심층 분석 컨설팅\n성공 사례 기반 사업화 트렌드 분석 등', 'icon' => 'main/pricing/addon-concierge.svg'],
    ['title' => '파트너잇 API+', 'description' => 'API/AI 데이터 연동 라이선스 제공', 'icon' => 'main/pricing/addon-api-icon.svg'],
    ['title' => '컨소시엄 프라임+', 'description' => '파트너 매칭 시 상위 리스트 추천\n매칭 성공 시 리워드 추가 지급', 'icon' => 'main/pricing/addon-prime.svg'],
    ['title' => '성과 활용 지원 서비스', 'description' => '조달 적합 기업 대상 컨설팅 연계\n성과 기반 투자/유관 사업 연계까지 후속 지원', 'icon' => 'main/pricing/addon-success.svg'],
];
?>
<section class="partnerit-pricing no-section-lg" aria-labelledby="partnerit-pricing-title">
    <div class="partnerit-pricing__inner no-container-xl">
        <header class="partnerit-pricing__header">
            <h2 class="partnerit-pricing__title f-heading-2 --bold" id="partnerit-pricing-title">
                <span>기업 성장 단계에 맞춘</span>
                <span>차등화된 구독 플랜을 제공합니다.</span>
            </h2>
        </header>

        <ul class="partnerit-pricing__plans" aria-label="구독 플랜">
            <?php foreach ($plans as $plan): ?>
                <li class="partnerit-pricing__plan partnerit-pricing__plan--<?= e($plan['tone']) ?>">
                    <article class="partnerit-pricing__plan-card">
                        <header class="partnerit-pricing__plan-head">
                            <span class="partnerit-pricing__badge f-body-3 --semibold"><?= e($plan['name']) ?></span>
                            <h3 class="partnerit-pricing__plan-title f-heading-5 --bold">
                                <?php foreach ($plan['headline'] as $line): ?>
                                    <span><?= e($line) ?></span>
                                <?php endforeach; ?>
                            </h3>
                            <p class="partnerit-pricing__price f-heading-pricing-price --bold">
                                <span><?= e($plan['price']) ?></span>
                                <?php if (!empty($plan['price_suffix'])): ?><small class="f-heading-5 --bold"><?= e($plan['price_suffix']) ?></small><?php endif; ?>
                            </p>
                        </header>

                        <div class="partnerit-pricing__plan-body">
                            <section class="partnerit-pricing__feature-group">
                                <h4 class="partnerit-pricing__group-title f-body-2 --bold">주요 기능</h4>
                                <ul class="partnerit-pricing__feature-list">
                                    <?php foreach ($plan['features'] as $feature): ?>
                                        <li class="partnerit-pricing__feature f-body-2">
                                            <?= image_picture('main/pricing/feature-check.svg', ['alt' => '', 'class' => 'partnerit-pricing__check']) ?>
                                            <span><?= e($feature) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </section>
                            <section class="partnerit-pricing__feature-group">
                                <h4 class="partnerit-pricing__group-title f-body-2 --bold">최대 규모</h4>
                                <p class="partnerit-pricing__feature f-body-2">
                                    <?= image_picture('main/pricing/feature-check.svg', ['alt' => '', 'class' => 'partnerit-pricing__check']) ?>
                                    <span><?= e($plan['scale']) ?></span>
                                </p>
                            </section>
                        </div>

                        <ul class="partnerit-pricing__perks" aria-label="<?= e($plan['name']) ?> 플랜 제공 기능">
                            <?php foreach ($plan['perks'] as $perk): ?>
                                <li class="partnerit-pricing__perk f-body-4 --semibold">
                                    <?= image_picture('main/pricing/' . $perkIcons[$perk] . '.svg', ['alt' => '', 'class' => 'partnerit-pricing__perk-icon']) ?>
                                    <span><?= e($perk) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>

        <section class="partnerit-pricing__addons" aria-labelledby="partnerit-pricing-addons-title">
            <h3 class="partnerit-pricing__addons-title f-heading-pricing-price --bold" id="partnerit-pricing-addons-title">부가 서비스</h3>
            <ul class="partnerit-pricing__addon-grid">
                <?php foreach ($addons as $addon): ?>
                    <li class="partnerit-pricing__addon">
                        <?= image_picture($addon['icon'], ['alt' => '', 'class' => 'partnerit-pricing__addon-icon']) ?>
                        <h4 class="partnerit-pricing__addon-title f-heading-5 --bold"><?= e($addon['title']) ?></h4>
                        <p class="partnerit-pricing__addon-desc f-body-3"><?= nl2br(e(str_replace('\\n', "\n", $addon['description']))) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </div>
</section>
