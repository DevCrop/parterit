<?php
$problemCards = [
    [
        'modifier' => 'closed-network',
        'icon' => 'business/consortium/problem/closed-network.png',
        'title' => '카르텔식 폐쇄 네트워크',
        'description' => "기존 네트워크 중심의 폐쇄형\n카르텔식 컨소시엄이 만연해 진입장벽 존재",
    ],
    [
        'modifier' => 'network-gap',
        'icon' => 'business/consortium/problem/network-gap.png',
        'title' => '인적 네트워크 부족',
        'description' => "기술력이 있음에도 불구하고\n적합한 파트너를 찾지 못해\n지원을 포기하는 사례 다수",
    ],
    [
        'modifier' => 'matching-gap',
        'icon' => 'business/consortium/problem/matching-gap.png',
        'title' => '파트너 매칭 시스템 부재',
        'description' => "기업 간 기술역량과\n목적이 맞는 파트너를 찾을\n공식적인 채널 전무",
    ],
];
?>
<section class="partnerit-consortium-problem" id="consortium-problem" aria-labelledby="partnerit-consortium-problem-title">
    <div class="partnerit-consortium-problem__inner no-container-xl">
        <header class="partnerit-consortium-problem__header">
            <h2 class="partnerit-consortium-problem__title f-heading-2 --bold" id="partnerit-consortium-problem-title">
                <span>기존 컨소시엄의 고착화와</span>
                <span>새로운 파트너 발굴의 어려움</span>
            </h2>
            <p class="partnerit-consortium-problem__intro f-body-2">
                <span>정부 R&amp;D 과제의 약 43% 이상이 컨소시엄 구성을 제도적으로 요구하고 있으나,</span>
                <span>기업들이 적합한 파트너를 찾지 못해 참여를 포기하는 문제와</span>
                <span>전략적 컨소시엄 구성 시스템 부재가 소기업의 참여를 저해한다는 분석이 지속적으로 제시되고 있습니다.</span>
            </p>
        </header>

        <ul class="partnerit-consortium-problem__grid">
            <?php foreach ($problemCards as $card): ?>
                <li class="partnerit-consortium-problem__card">
                    <figure class="partnerit-consortium-problem__icon partnerit-consortium-problem__icon--<?= e($card['modifier']) ?>" aria-hidden="true">
                        <?= image_picture($card['icon'], [
                            'alt' => '',
                            'loading' => 'lazy',
                            'decoding' => 'async',
                            'class' => 'partnerit-consortium-problem__icon-image',
                        ]) ?>
                    </figure>
                    <div class="partnerit-consortium-problem__card-copy">
                        <h3 class="partnerit-consortium-problem__card-title f-heading-5 --bold"><?= e($card['title']) ?></h3>
                        <p class="partnerit-consortium-problem__card-description f-body-2"><?= e_nl2br_html($card['description']) ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
