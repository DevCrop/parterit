<?php
$partnerRows = [
    [
        'direction' => 'forward',
        'logos' => [
            'main/partners/figma-logo-01.png',
            'main/partners/figma-logo-02.png',
            'main/partners/figma-logo-03.png',
            'main/partners/figma-logo-04.png',
            'main/partners/figma-logo-05.png',
            'main/partners/figma-logo-06.png',
            'main/partners/figma-logo-07.png',
            'main/partners/figma-logo-08.png',
        ],
    ],
    [
        'direction' => 'reverse',
        'logos' => [
            'main/partners/figma-logo-09.png',
            'main/partners/figma-logo-10.png',
            'main/partners/figma-logo-11.png',
            'main/partners/figma-logo-12.png',
            'main/partners/figma-logo-13.png',
            'main/partners/figma-logo-14.png',
            'main/partners/figma-logo-15.png',
            'main/partners/figma-logo-16.png',
        ],
    ],
];
?>
<section class="partnerit-partners no-section-lg" aria-labelledby="partnerit-partners-title">
    <header class="partnerit-partners__header no-container-xl">
        <h2 class="partnerit-partners__title f-heading-1 --bold" id="partnerit-partners-title">기술 자산과 파트너십으로 쌓아가는 신뢰</h2>
        <p class="partnerit-partners__intro f-body-2">수많은 기관과 기업이 파트너잇과 함께 새로운 기회를 만들어가고 있습니다.</p>
    </header>

    <div class="partnerit-partners__marquees" aria-label="파트너사 로고">
        <?php foreach ($partnerRows as $row): ?>
            <div class="partnerit-partners__marquee partnerit-partners__marquee--<?= e($row['direction']) ?>">
                <div class="partnerit-partners__track">
                    <?php for ($copy = 0; $copy < 2; $copy++): ?>
                        <ul class="partnerit-partners__list"<?= $copy === 1 ? ' aria-hidden="true"' : '' ?>>
                            <?php foreach ($row['logos'] as $logo): ?>
                                <li class="partnerit-partners__item">
                                    <?= image_picture($logo, [
                                        'alt' => '',
                                        'class' => 'partnerit-partners__logo',
                                        'loading' => 'eager',
                                        'decoding' => 'async',
                                    ]) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
