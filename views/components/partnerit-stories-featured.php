<?php
require_once __DIR__ . '/partnerit-stories-data.php';

$cmsStories = partnerit_customer_stories();
$fallbackFeaturedStory = [
    'image' => 'featured.png', 'category' => '대표 고객 이야기 · 스타트업 / 사진 시안',
    'title' => '“지원사업 탐색부터 AI 리포트까지”',
    'titleLines' => ['“지원사업 탐색부터', 'AI 리포트까지”'],
    'description' => '지원사업 탐색부터 AI 리포트까지, 파트너잇 덕분에 소요 시간이 엄청나게 단축되었습니다. 기대 이상의 효율성과 만족입니다. 감사합니다.',
    'author' => '애기***', 'date' => '2026.05.03',
];
$featuredStories = array_slice(partnerit_featured_customer_stories($cmsStories), 0, 5);
$featuredStories = $featuredStories !== [] ? $featuredStories : [$fallbackFeaturedStory];
$featuredCount = count($featuredStories);
?>
<section class="partnerit-stories-featured" aria-label="대표 고객 경험">
    <div class="partnerit-stories-featured__inner no-container-xl">
        <div class="partnerit-stories-featured__swiper swiper" id="partnerit-stories-featured-swiper" data-partnerit-stories-featured-swiper role="region" aria-label="대표 고객사례" aria-roledescription="carousel">
            <ul class="partnerit-stories-featured__list swiper-wrapper">
                <?php foreach ($featuredStories as $index => $featuredStory): ?>
                    <li class="partnerit-stories-featured__slide swiper-slide" role="group" aria-roledescription="slide" aria-label="<?= $index + 1 ?> / <?= $featuredCount ?>">
                        <?= include_view('components.partnerit-story-card', [
                            'story' => $featuredStory,
                            'featured' => true,
                            'eager' => $index === 0,
                        ]) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php if ($featuredCount > 1): ?>
        <nav class="partnerit-stories-featured__controls partnerit-swiper-controls" aria-label="대표 고객사례 슬라이드 제어">
            <button class="partnerit-stories-featured__button partnerit-stories-featured__button--prev partnerit-swiper-controls__button partnerit-swiper-controls__button--prev" type="button" aria-label="이전 대표 고객사례" aria-controls="partnerit-stories-featured-swiper">
                <img src="<?= e(img_path('main/reviews/arrow-control-prev.svg')) ?>" alt="" aria-hidden="true">
            </button>
            <div class="partnerit-stories-featured__pagination partnerit-swiper-controls__pagination" role="group" aria-label="대표 고객사례 페이지 선택"></div>
            <button class="partnerit-stories-featured__button partnerit-stories-featured__button--next partnerit-swiper-controls__button partnerit-swiper-controls__button--next" type="button" aria-label="다음 대표 고객사례" aria-controls="partnerit-stories-featured-swiper">
                <img src="<?= e(img_path('main/reviews/arrow-control-next.svg')) ?>" alt="" aria-hidden="true">
            </button>
        </nav>
        <?php endif; ?>
    </div>
</section>
