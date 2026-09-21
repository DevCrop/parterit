<?php
require_once __DIR__ . '/partnerit-stories-data.php';

$cmsStories = partnerit_customer_stories();
$featuredStories = partnerit_featured_customer_stories($cmsStories);
$featuredIds = array_map(static fn(array $story): int => (int) ($story['id'] ?? 0), $featuredStories);

$allStories = $cmsStories !== []
    ? array_values(array_filter($cmsStories, static fn(array $story): bool => !in_array((int) ($story['id'] ?? 0), $featuredIds, true)))
    : [
    ['image' => 'portrait-public.png', 'category' => 'HR 테크기업 · 사진 시안', 'title' => '우리 회사에 딱 맞는 지원사업', 'description' => '파트너잇 덕분에 우리 회사에 딱 맞는 지원사업을 빠르게 찾았습니다. 복잡했던 과정이 한번에 해결되어 시간 절약에 큰 도움이 되는 것 같아요.', 'author' => '브이**', 'date' => '2026.04.05'],
    ['image' => 'portrait-venture.png', 'category' => '벤처기업 · 사진 시안', 'title' => '핵심 업무에 더 집중할 수 있게', 'description' => '번거로운 공고 조회 과정을 파트너잇이 간편하게 처리해주니 핵심 업무에 집중할 수 있게 되었습니다. 사용 편의성 면에서 최고의 솔루션입니다.', 'author' => '엔*', 'date' => '2026.04.14'],
    ['image' => 'featured.png', 'category' => '공공/복지기관 · 사진 시안', 'title' => '지원할 수 있는 사업만 한눈에', 'description' => '한번 사용해보라고 해서 해봤는데 너무 편합니다. 자격요건에 맞는 사업을 일일이 찾았어야 했는데, 진짜 저희 지원할 수 있는 사업만 한번에 보이네요 정말 유용합니다.', 'author' => '부산*****', 'date' => '2026.04.18'],
    ['image' => 'portrait-public.png', 'category' => '신생기업 · 사진 시안', 'title' => '막막했던 지원사업 준비에 도움', 'description' => '법인 설립한지 얼마 되지 않아서, 무슨 사업을 넣어야 할지, 어떻게 해야 할지 막막했는데, 큰 도움을 얻은 것 같습니다. 비록 이번에는 떨어졌지만, 다음번에는 꼭 파트너잇과 성공하고 싶습니다.', 'author' => '랑구***', 'date' => '2026.05.18'],
    ['image' => 'portrait-venture.png', 'category' => '스타트업 · 사진 시안', 'title' => '정식 출시를 기다리고 있어요', 'description' => '정식 출시일만 기다리고 있어요.', 'author' => '헬퍼*', 'date' => '2026.04.13'],
];

$perPage = 6;
$totalStories = count($allStories);
$totalPages = max(1, (int) ceil($totalStories / $perPage));
$currentPage = max(1, min($totalPages, (int) ($_GET['story_page'] ?? 1)));
$stories = array_slice($allStories, ($currentPage - 1) * $perPage, $perPage);
$paginationUrl = static fn(int $page): string => route('partners', ['story_page' => $page]) . '#partnerit-stories-gallery-title';
?>
<?php if ($stories !== []): ?>
<section class="partnerit-stories-gallery" aria-labelledby="partnerit-stories-gallery-title">
    <div class="partnerit-stories-gallery__inner no-container-xl">
        <header class="partnerit-stories-gallery__heading">
            <h2 class="f-heading-announcement-section" id="partnerit-stories-gallery-title">다양한 기업이 전하는 이용 경험</h2>
            <p class="f-body-announcement-copy">파트너잇을 직접 사용해 본 고객의 목소리입니다.</p>
        </header>
        <div class="partnerit-stories-gallery__grid">
            <?php foreach ($stories as $story): ?>
                <?= include_view('components.partnerit-story-card', ['story' => $story, 'featured' => false]) ?>
            <?php endforeach; ?>
        </div>
        <nav class="partnerit-stories-gallery__pagination" aria-label="고객사례 페이지">
            <?php if ($currentPage > 1): ?>
                <a class="partnerit-stories-gallery__arrow partnerit-stories-gallery__arrow--prev" href="<?= e($paginationUrl($currentPage - 1)) ?>" rel="prev" aria-label="이전 페이지"><?= image_picture('stories/arrow-dark.svg', ['alt' => '']) ?></a>
            <?php else: ?>
                <span class="partnerit-stories-gallery__arrow partnerit-stories-gallery__arrow--prev is-disabled" aria-hidden="true"><?= image_picture('stories/arrow-dark.svg', ['alt' => '']) ?></span>
            <?php endif; ?>

            <?php for ($pageNumber = 1; $pageNumber <= $totalPages; $pageNumber++): ?>
                <?php if ($pageNumber === $currentPage): ?>
                    <span class="partnerit-stories-gallery__page f-body-stories-badge" aria-current="page"><?= $pageNumber ?></span>
                <?php else: ?>
                    <a class="partnerit-stories-gallery__page f-body-stories-badge" href="<?= e($paginationUrl($pageNumber)) ?>" aria-label="<?= $pageNumber ?>페이지"><?= $pageNumber ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a class="partnerit-stories-gallery__arrow partnerit-stories-gallery__arrow--next" href="<?= e($paginationUrl($currentPage + 1)) ?>" rel="next" aria-label="다음 페이지"><?= image_picture('stories/arrow-light.svg', ['alt' => '']) ?></a>
            <?php else: ?>
                <span class="partnerit-stories-gallery__arrow partnerit-stories-gallery__arrow--next is-disabled" aria-hidden="true"><?= image_picture('stories/arrow-light.svg', ['alt' => '']) ?></span>
            <?php endif; ?>
        </nav>
    </div>
</section>
<?php endif; ?>
