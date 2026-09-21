<?php
$isFeatured = (bool) ($featured ?? false);
$isEager = (bool) ($eager ?? $isFeatured);
$story = $story ?? [];
$imageInput = !empty($story['image_is_url'])
    ? (string) ($story['image'] ?? '')
    : 'stories/' . (string) ($story['image'] ?? '');
$imageAlt = trim((string) ($story['image_alt'] ?? ''));
$imageAlt = $imageAlt !== '' ? $imageAlt : '고객사례 이미지';
$storyHref = trim((string) ($story['href'] ?? ''));
?>
<article class="partnerit-story-card<?= $isFeatured ? ' partnerit-story-card--featured' : '' ?>">
    <?php if ($storyHref !== ''): ?>
    <a class="partnerit-story-card__link" href="<?= e($storyHref) ?>" aria-label="<?= e((string) ($story['title'] ?? '고객사례')) ?> 자세히 보기">
    <?php endif; ?>
    <figure class="partnerit-story-card__photo">
        <?= !empty($story['image_is_url'])
            ? picture_from_url($imageInput, (string) ($story['image_webp'] ?? ''), ['alt' => $imageAlt, 'loading' => $isEager ? 'eager' : 'lazy', 'decoding' => 'async'])
            : image_picture($imageInput, ['alt' => $imageAlt, 'loading' => $isEager ? 'eager' : 'lazy', 'decoding' => 'async']) ?>
    </figure>
    <div class="partnerit-story-card__body">
        <span class="partnerit-story-card__badge f-body-stories-badge"><?= e((string) ($story['category'] ?? '고객사례')) ?></span>
        <h3 class="partnerit-story-card__title <?= $isFeatured ? 'f-heading-stories-featured' : 'f-heading-stories-card' ?>"><?php foreach (($story['titleLines'] ?? [(string) ($story['title'] ?? '')]) as $line): ?><span><?= e((string) $line) ?></span><?php endforeach; ?></h3>
        <p class="partnerit-story-card__description <?= $isFeatured ? 'f-body-announcement-copy' : 'f-body-stories-copy' ?>"><?= e((string) ($story['description'] ?? '')) ?></p>
        <p class="partnerit-story-card__meta f-body-stories-meta"><span><?= e((string) ($story['author'] ?? '')) ?></span><?php if (!empty($story['date'])): ?><span aria-hidden="true"> | </span><time datetime="<?= e(str_replace('.', '-', (string) $story['date'])) ?>"><?= e((string) $story['date']) ?></time><?php endif; ?></p>
    </div>
    <?php if ($storyHref !== ''): ?>
    </a>
    <?php endif; ?>
</article>
