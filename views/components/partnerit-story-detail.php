<?php
$story = is_array($story ?? null) ? $story : null;
?>
<section class="partnerit-story-detail no-section-lg">
    <div class="partnerit-story-detail__inner no-container-xl">
        <?php if ($story): ?>
            <a class="partnerit-story-detail__back f-body-2 --semibold" href="<?= e(route('partners')) ?>">고객사례 목록</a>
            <header class="partnerit-story-detail__header">
                <span class="partnerit-story-detail__badge f-body-stories-badge"><?= e((string) ($story['category'] ?? '고객사례')) ?></span>
                <h1 class="partnerit-story-detail__title f-heading-1 --bold"><?= e((string) ($story['title'] ?? '')) ?></h1>
                <p class="partnerit-story-detail__meta f-body-2">
                    <strong><?= e((string) ($story['author'] ?? '')) ?></strong>
                    <?php if (!empty($story['date'])): ?><span aria-hidden="true">·</span><time datetime="<?= e(str_replace('.', '-', (string) $story['date'])) ?>"><?= e((string) $story['date']) ?></time><?php endif; ?>
                </p>
            </header>
            <?php if (!empty($story['image'])): ?>
                <figure class="partnerit-story-detail__visual">
                    <?= picture_from_url((string) $story['image'], (string) ($story['image_webp'] ?? ''), [
                        'alt' => (string) ($story['image_alt'] ?? ''),
                        'decoding' => 'async',
                        'fetchpriority' => 'high',
                    ]) ?>
                </figure>
            <?php endif; ?>
            <div class="partnerit-story-detail__content f-body-1">
                <?= include_view('components.summernote-content', ['html' => (string) ($story['contents_html'] ?? '')]) ?>
            </div>
        <?php else: ?>
            <div class="partnerit-story-detail__empty">
                <h1 class="f-heading-2 --bold">고객사례를 찾을 수 없습니다.</h1>
                <a class="no-btn no-btn--main" href="<?= e(route('partners')) ?>">목록으로 돌아가기</a>
            </div>
        <?php endif; ?>
    </div>
</section>
