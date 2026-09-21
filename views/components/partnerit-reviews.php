<?php
require_once __DIR__ . '/partnerit-stories-data.php';

$figmaReviews = [
    [
        'category' => '이용 후기',
        'quote' => '지원사업 탐색부터 AI 리포트까지, 파트너잇 덕분에 소요 시간이 엄청나게 단축되었습니다.',
        'client' => '애기***',
        'role' => '스타트업 / 사진 시안',
        'image' => 'main/reviews/review-handhug-kim-jaehyun.png',
        'image_alt' => '스타트업 이용 후기 사진 시안',
        'image_position' => 'center',
    ],
    [
        'category' => '이용 후기',
        'quote' => '번거로운 공고 조회 과정을 파트너잇이 간편하게 처리해주니 핵심 업무에 집중할 수 있게 되었습니다. 사용 편의성 면에서 최고의 솔루션입니다.',
        'client' => '엔*',
        'role' => '벤처기업 / 사진 시안',
        'image' => 'stories/portrait-venture.png',
        'image_alt' => '벤처기업 이용 후기 사진 시안',
        'image_position' => 'right',
    ],
    [
        'category' => '이용 후기',
        'quote' => '파트너잇 덕분에 우리 회사에 딱 맞는 지원사업을 빠르게 찾았습니다. 복잡했던 과정이 한번에 해결되어 시간 절약에 큰 도움이 되는 것 같아요.',
        'client' => '브이**',
        'role' => 'HR 테크기업 / 사진 시안',
        'image' => 'stories/portrait-public.png',
        'image_alt' => 'HR 테크기업 이용 후기 사진 시안',
        'image_position' => 'left',
    ],
    [
        'category' => '이용 후기',
        'quote' => '정식 출시일만 기다리고 있어요.',
        'client' => '헬퍼*',
        'role' => '스타트업 / 사진 시안',
        'image' => 'stories/portrait-venture.png',
        'image_alt' => '스타트업 이용 후기 사진 시안',
        'image_position' => 'right',
    ],
    [
        'category' => '이용 후기',
        'quote' => '법인 설립한지 얼마 되지 않아서, 무슨 사업을 넣어야 할지, 어떻게 해야 할지 막막했는데, 큰 도움을 얻은 것 같습니다. 비록 이번에는 떨어졌지만, 다음번에는 꼭 파트너잇과 성공하고 싶습니다.',
        'client' => '랑구***',
        'role' => '신생기업 / 사진 시안',
        'image' => 'stories/portrait-public.png',
        'image_alt' => '신생기업 이용 후기 사진 시안',
        'image_position' => 'left',
    ],
];

$featuredCmsStories = array_slice(
    partnerit_featured_customer_stories(partnerit_customer_stories()),
    0,
    5
);

$cmsReviews = array_map(static function (array $story): array {
    $title = trim((string) ($story['title'] ?? ''));
    $description = trim((string) ($story['description'] ?? ''));

    return [
        'category' => trim((string) ($story['category'] ?? '고객사례')),
        'quote' => $title !== '' ? $title : $description,
        'client' => trim((string) ($story['author'] ?? '')),
        'role' => trim((string) ($story['category'] ?? '')),
        'image' => trim((string) ($story['image'] ?? '')),
        'image_webp' => trim((string) ($story['image_webp'] ?? '')),
        'image_is_url' => !empty($story['image_is_url']),
        'image_alt' => trim((string) ($story['image_alt'] ?? '고객사례 이미지')),
        'image_position' => 'center',
        'href' => trim((string) ($story['href'] ?? '')),
    ];
}, $featuredCmsStories);

$reviews = isset($reviews) && is_array($reviews) && $reviews !== []
    ? array_slice(array_values($reviews), 0, 5)
    : ($cmsReviews !== [] ? $cmsReviews : $figmaReviews);
$reviews = array_slice($reviews, 0, 5);
$reviewCount = count($reviews);
$allowedImagePositions = ['center', 'left', 'right'];
?>
<section class="partnerit-reviews no-section-lg" aria-labelledby="partnerit-reviews-title">
    <header class="partnerit-reviews__header no-container-xl">
        <h2 class="partnerit-reviews__title f-heading-1 --bold" id="partnerit-reviews-title">고객들이 직접 경험한 변화</h2>
        <p class="partnerit-reviews__intro f-body-2">
            공고 탐색과 분석, 협업 과정에서 고객이 경험한 변화를 확인해 보세요.
        </p>
    </header>

    <div class="partnerit-reviews__carousel">
        <div class="partnerit-reviews__swiper swiper"
            id="partnerit-reviews-swiper"
            data-partnerit-reviews-swiper
            role="region"
            aria-label="파트너잇 고객 후기"
            aria-roledescription="carousel">
            <ul class="partnerit-reviews__list swiper-wrapper">
                <?php foreach ($reviews as $index => $review): ?>
                    <?php
                    $category = trim((string) ($review['category'] ?? ''));
                    $quote = trim((string) ($review['quote'] ?? ''));
                    $client = trim((string) ($review['client'] ?? ''));
                    $role = trim((string) ($review['role'] ?? ''));
                    $image = trim((string) ($review['image'] ?? ''));
                    $imageWebp = trim((string) ($review['image_webp'] ?? ''));
                    $imageIsUrl = !empty($review['image_is_url']);
                    $imageAlt = trim((string) ($review['image_alt'] ?? ''));
                    $href = trim((string) ($review['href'] ?? ''));
                    $imagePosition = (string) ($review['image_position'] ?? 'center');
                    $imagePosition = in_array($imagePosition, $allowedImagePositions, true) ? $imagePosition : 'center';
                    $hasCopy = $quote !== '' || $client !== '' || $role !== '';
                    ?>
                    <li class="partnerit-reviews__slide swiper-slide" role="group" aria-roledescription="slide" aria-label="<?= (int) $index + 1 ?> / <?= $reviewCount ?>">
                        <article class="partnerit-reviews__card">
                            <?php if ($image !== ''): ?>
                                <?= $imageIsUrl
                                    ? picture_from_url($image, $imageWebp, [
                                        'alt' => $imageAlt,
                                        'class' => 'partnerit-reviews__image partnerit-reviews__image--' . $imagePosition,
                                        'loading' => $index === 0 ? 'eager' : 'lazy',
                                        'decoding' => 'async',
                                    ])
                                    : image_picture($image, [
                                        'alt' => $imageAlt,
                                        'class' => 'partnerit-reviews__image partnerit-reviews__image--' . $imagePosition,
                                        'loading' => $index === 0 ? 'eager' : 'lazy',
                                        'decoding' => 'async',
                                    ]) ?>
                            <?php endif; ?>

                            <span class="partnerit-reviews__shade" aria-hidden="true"></span>

                            <?php if ($href !== ''): ?>
                                <a class="partnerit-reviews__detail-link" href="<?= e($href) ?>" aria-label="<?= e($quote !== '' ? $quote : '고객사례') ?> 자세히 보기"></a>
                            <?php endif; ?>

                            <?php if ($category !== ''): ?>
                                <span class="partnerit-reviews__badge f-body-4 --semibold"><?= e($category) ?></span>
                            <?php endif; ?>

                            <?php if ($hasCopy): ?>
                                <div class="partnerit-reviews__copy">
                                    <?php if ($quote !== ''): ?>
                                        <blockquote class="partnerit-reviews__quote f-body-1 --bold">
                                            <p><?= e($quote) ?></p>
                                        </blockquote>
                                    <?php endif; ?>

                                    <?php if ($client !== '' || $role !== ''): ?>
                                        <footer class="partnerit-reviews__attribution f-body-4 --bold">
                                            <?php if ($client !== ''): ?>
                                                <span class="partnerit-reviews__client"><?= e($client) ?></span>
                                            <?php endif; ?>
                                            <?php if ($role !== ''): ?>
                                                <span class="partnerit-reviews__role"><?= e($role) ?></span>
                                            <?php endif; ?>
                                        </footer>
                                    <?php endif; ?>
                                </div>

                            <?php endif; ?>

                            <button class="partnerit-reviews__card-next" type="button" data-partnerit-review-next aria-label="다음 고객 후기">
                                <img src="<?= e(img_path('main/reviews/arrow-card-next.svg')) ?>" alt="" aria-hidden="true">
                            </button>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <nav class="partnerit-reviews__controls partnerit-swiper-controls" aria-label="후기 슬라이드 제어">
        <button class="partnerit-reviews__button partnerit-reviews__button--prev partnerit-swiper-controls__button partnerit-swiper-controls__button--prev" type="button" aria-label="이전 후기" aria-controls="partnerit-reviews-swiper">
            <img src="<?= e(img_path('main/reviews/arrow-control-prev.svg')) ?>" alt="" aria-hidden="true">
        </button>
        <div class="partnerit-reviews__pagination partnerit-swiper-controls__pagination" role="group" aria-label="후기 페이지 선택"></div>
        <button class="partnerit-reviews__button partnerit-reviews__button--next partnerit-swiper-controls__button partnerit-swiper-controls__button--next" type="button" aria-label="다음 후기" aria-controls="partnerit-reviews-swiper">
            <img src="<?= e(img_path('main/reviews/arrow-control-next.svg')) ?>" alt="" aria-hidden="true">
        </button>
    </nav>
</section>
