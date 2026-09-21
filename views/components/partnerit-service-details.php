<?php
$serviceDetails = [
    [
        'alt' => 'AI 기업 분석 대시보드와 로봇 도우미',
        'title' => ['AI로 시간 절약,', '맞는 기회만 빠르게'],
        'tags' => ['#AI정밀분석', '#자동매칭', '#파트너연결'],
        'image' => 'service-ai-matching-v2.png',
        'width' => 610,
        'height' => 368,
    ],
    [
        'alt' => '지원서 분석과 정보 정리 대시보드',
        'title' => ['회사와 공고를 연결,', '기회를 놓치지 않도록'],
        'tags' => ['#데이터연결', '#정보통합', '#기회도출'],
        'image' => 'service-business-link-v2.png',
        'width' => 579,
        'height' => 302,
    ],
    [
        'alt' => 'AI 회의록을 생성하는 회의 화면',
        'title' => ['반복 업무는 줄이고,', '핵심 전략에 집중'],
        'tags' => ['#업무자동화', '#시간절약', '#생산성향상'],
        'image' => 'service-productivity-v2.png',
        'width' => 583,
        'height' => 366,
    ],
    [
        'alt' => '협업 앱과 기업 미팅 일정을 연결한 화면',
        'title' => ['사업 이후의 기회까지,', '더 큰 성장으로 연결'],
        'tags' => ['#VC/AC 매칭', '#수요기관 연결', '#기업전주기'],
        'image' => 'service-growth-v2.png',
        'width' => 660,
        'height' => 462,
    ],
];
?>
<section class="partnerit-service-details no-section-lg" aria-labelledby="partnerit-service-details-title">
    <div class="partnerit-service-details__inner no-container-xl">
        <div class="partnerit-service-details__objects" aria-hidden="true">
            <img
                class="partnerit-service-details__object partnerit-service-details__object--one"
                src="<?= e(base_path('/resource/images/main/service-details/service-details-obj-1-v2.png')) ?>"
                alt=""
                width="1254"
                height="1254"
                loading="lazy"
                decoding="async"
            >
            <img
                class="partnerit-service-details__object partnerit-service-details__object--two"
                src="<?= e(base_path('/resource/images/main/service-details/service-details-obj-2-v2.png')) ?>"
                alt=""
                width="1254"
                height="1254"
                loading="lazy"
                decoding="async"
            >
        </div>

        <header class="partnerit-service-details__header">
            <h2 class="partnerit-service-details__title f-heading-1 --bold" id="partnerit-service-details-title">Services Details</h2>
            <p class="partnerit-service-details__intro f-body-1">
                파트너잇은 단순 공고 추천 서비스가 아니라<br>
                컨소시엄 구성 후 실제 프로젝트 실행까지 연결되는 통합 경험을 지향합니다.
            </p>
        </header>

        <ul class="partnerit-service-details__grid" aria-label="파트너잇 서비스 상세">
            <?php foreach ($serviceDetails as $service): ?>
                <li class="partnerit-service-details__item">
                    <article class="partnerit-service-details__card">
                        <div class="partnerit-service-details__body">
                            <h3 class="partnerit-service-details__item-title f-heading-5 --bold">
                                <?= e($service['title'][0]) ?><br>
                                <?= e($service['title'][1]) ?>
                            </h3>
                            <ul class="partnerit-service-details__tags" aria-label="서비스 특징">
                                <?php foreach ($service['tags'] as $tag): ?>
                                    <li class="partnerit-service-details__tag f-body-3"><?= e($tag) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <figure class="partnerit-service-details__media">
                            <img
                                class="partnerit-service-details__image"
                                src="<?= e(base_path('/resource/images/main/service-details/' . $service['image'])) ?>"
                                alt="<?= e($service['alt']) ?>"
                                width="<?= e((string) $service['width']) ?>"
                                height="<?= e((string) $service['height']) ?>"
                                loading="lazy"
                                decoding="async"
                            >
                        </figure>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
