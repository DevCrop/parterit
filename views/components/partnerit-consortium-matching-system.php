<?php
$matchingPaths = [
    ['label' => '파트너 모집', 'title' => '부족한 역량을 채울 협력기업 찾기', 'description' => '필요한 역할에 맞는 기업을 추천받고, 기업정보와 수행이력을 확인합니다.'],
    ['label' => '컨소시엄 참여', 'title' => '우리 역량이 필요한 컨소시엄 찾기', 'description' => '보유 기술에 맞는 컨소시엄을 추천받고, 모집 내용과 필요한 역할을 살펴봅니다.'],
];
?>
<section class="partnerit-consortium-matching" id="consortium-matching-system" aria-labelledby="consortium-matching-system-title">
    <div class="partnerit-consortium-matching__inner no-container-xl">
        <header class="partnerit-consortium-matching__header">
            <h2 class="partnerit-consortium-matching__title f-heading-2 --bold" id="consortium-matching-system-title">
                <span>분석부터 연결까지,</span>
                <span>필요한 파트너를 AI가 자동으로 매칭</span>
            </h2>
            <p class="partnerit-consortium-matching__lead f-body-2">
                단순 키워드 필터링이 아닌, 기술 의미 기반의 역할·기업 연결 분석을 통해 컨소시엄 매칭의 정확도와 공정성을 향상
            </p>
        </header>

        <div class="partnerit-consortium-matching__content">
            <figure class="partnerit-consortium-matching__visual" aria-label="파트너잇 컨소시엄 모집 현황 화면">
                <div class="partnerit-consortium-matching__glow" aria-hidden="true"></div>
                <div class="partnerit-consortium-matching__panel">
                    <div class="partnerit-consortium-matching__panel-motion">
                        <?= image_picture('business/consortium/matching-system/matching-panel.png', [
                            'alt' => '컨소시엄 모집 현황과 참여 신청 기능 화면',
                            'loading' => 'lazy',
                            'decoding' => 'async',
                            'class' => 'partnerit-consortium-matching__panel-image',
                        ]) ?>
                    </div>
                </div>
            </figure>

            <div class="partnerit-consortium-matching__explanation">
                <div class="partnerit-consortium-matching__copy">
                    <h3 class="partnerit-consortium-matching__feature-title f-heading-consortium-feature --bold">컨소시엄 매칭 시스템</h3>
                    <p class="partnerit-consortium-matching__description f-body-2">
                        매칭룸에 필요한 기술역량과 기업의 보유 역량을 분석해,
                        협력기업과 참여 가능한 컨소시엄을 <strong>양방향으로 추천합니다.</strong>
                    </p>
                </div>
                <ul class="partnerit-consortium-matching__steps" aria-label="핵심 매칭 기능">
                    <?php foreach (['기술역량 분석', '협력기업 추천', '컨소시엄 추천', '양방향 매칭'] as $badge): ?>
                    <li class="partnerit-consortium-matching__step f-body-4 --medium"><?= e($badge) ?></li>
                    <?php endforeach; ?>
                </ul>

        <div class="partnerit-consortium-matching__paths">
            <?php foreach ($matchingPaths as $path): ?>
            <article class="partnerit-consortium-matching__path">
                <p class="partnerit-consortium-matching__path-label f-body-3 --bold"><?= e($path['label']) ?></p>
                <h3 class="f-heading-ai-report-label"><?= e($path['title']) ?></h3>
                <p class="partnerit-consortium-matching__description f-body-3"><?= e($path['description']) ?></p>
            </article>
            <?php endforeach; ?>
        </div>
            </div>
        </div>
    </div>
</section>
