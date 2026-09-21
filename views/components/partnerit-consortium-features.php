<?php
$consortiumFeatures = [
    [
        'modifier' => 'capability',
        'icon' => 'business/consortium/features/capability-matching.png',
        'title' => '기술역량 기반 파트너 매칭',
        'description' => '단순 키워드가 아닌 기술 의미와 역할을 기반으로 기업의 보유 역량을 분석해, 컨소시엄에 필요한 역할과 적합한 협력기업을 연결합니다.',
    ],
    [
        'modifier' => 'company',
        'icon' => 'business/consortium/features/company-list.png',
        'title' => '분야별 기업 리스트 추천',
        'description' => '기업별 보유 기술과 사업분야를 바탕으로 컨소시엄에 필요한 역량을 보유한 기업을 선별해 추천합니다.',
    ],
    [
        'modifier' => 'consortium',
        'icon' => 'business/consortium/features/consortium-list.png',
        'title' => '생성된 컨소시엄 리스트 추천',
        'description' => '특정 기업의 기술역량을 기준으로 참여 가능한 컨소시엄 리스트를 제안해, 기업과 컨소시엄을 양방향으로 연결합니다.',
    ],
    [
        'modifier' => 'trust',
        'icon' => 'business/consortium/features/trust-verification.png',
        'title' => '파트너 신뢰도 검증',
        'description' => '추천된 협력기업을 검토할 때 파트너 신뢰도 검증 기능을 제공해, 컨소시엄 구성 시 보다 신중하게 파트너를 선택할 수 있도록 돕습니다.',
    ],
];
?>
<section class="partnerit-consortium-features" id="consortium-features" aria-labelledby="consortium-features-title">
    <div class="partnerit-consortium-features__inner no-container-xl">
        <h2 class="partnerit-consortium-features__title f-heading-2 --bold" id="consortium-features-title">
            <span>AI 기반 기술역량 분석으로</span>
            <span>최적의 파트너 자동 매칭</span>
        </h2>

        <ul class="partnerit-consortium-features__grid">
            <?php foreach ($consortiumFeatures as $index => $feature): ?>
                <li class="partnerit-consortium-features__card">
                    <figure class="partnerit-consortium-features__icon partnerit-consortium-features__icon--<?= e($feature['modifier']) ?>" aria-hidden="true">
                        <?= image_picture($feature['icon'], [
                            'alt' => '',
                            'loading' => 'lazy',
                            'decoding' => 'async',
                            'class' => 'partnerit-consortium-features__icon-image',
                        ]) ?>
                    </figure>
                    <div class="partnerit-consortium-features__copy">
                        <h3 class="partnerit-consortium-features__card-title f-heading-consortium-card --bold"><?= e($feature['title']) ?></h3>
                        <p class="partnerit-consortium-features__description f-body-3 --medium"><?= e($feature['description']) ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
