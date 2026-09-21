<?php
/**
 * @var array<int,array<string,mixed>> $popups
 */
$popupSlides = [];

foreach ($popups as $popup) {
    $imageName = basename(str_replace('\\', '/', (string) ($popup['popup_image'] ?? '')));
    if ($imageName === '' || !is_file(ROOT_PATH . '/uploads/popups/' . $imageName)) {
        continue;
    }

    $rawUrl = trim((string) ($popup['link_url'] ?? ''));
    $linkUrl = ((int) ($popup['has_link'] ?? 2) === 1 && $rawUrl !== '' && function_exists('safe_url'))
        ? safe_url($rawUrl, ['http', 'https'], true)
        : '';

    $popupSlides[] = [
        'id' => (int) ($popup['id'] ?? 0),
        'title' => trim((string) ($popup['title'] ?? '')),
        'image' => $imageName,
        'link_url' => $linkUrl ?: '',
        'target' => ((int) ($popup['is_target'] ?? 1) === 1) ? '_blank' : '_self',
    ];
}

if (empty($popupSlides)) {
    return;
}

$popupIds = array_map(static fn(array $popup): int => (int) $popup['id'], $popupSlides);
?>

<div class="image-popup-wrap" data-popup-root data-popup-ids="<?= e(implode(',', $popupIds)) ?>" aria-modal="true" role="dialog" aria-label="Popup notice">
    <div class="image-popup-bg" data-popup-close aria-hidden="true"></div>
    <div class="image-popup" role="document">
        <div class="image-popup-top">
            <?php if (count($popupSlides) > 1): ?>
            <div class="swiper-component" aria-label="Popup navigation">
                <button type="button" class="image-popup-prev arrow" aria-label="Previous popup">
                    <i class="fa-regular fa-arrow-left" aria-hidden="true"></i>
                </button>
                <button type="button" class="image-popup-next arrow" aria-label="Next popup">
                    <i class="fa-regular fa-arrow-right" aria-hidden="true"></i>
                </button>
            </div>
            <?php endif; ?>
        </div>

        <div class="image-popup-mid swiper" data-popup-swiper>
            <ul class="swiper-wrapper">
                <?php foreach ($popupSlides as $popup): ?>
                <li class="swiper-slide" data-popup-slide data-popup-id="<?= (int) $popup['id'] ?>">
                    <?php if ($popup['link_url'] !== ''): ?>
                    <a class="img-box" href="<?= e($popup['link_url']) ?>"<?= $popup['target'] === '_blank' ? ' target="_blank" rel="noopener noreferrer"' : '' ?>>
                    <?php else: ?>
                    <span class="img-box">
                    <?php endif; ?>
                        <img src="<?= e(base_path('/uploads/popups/' . $popup['image'])) ?>" alt="<?= e($popup['title']) ?>" decoding="async">
                    <?php if ($popup['link_url'] !== ''): ?>
                    </a>
                    <?php else: ?>
                    </span>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <div class="image-popup-pagination"></div>
        </div>

        <div class="image-popup-bottom">
            <label class="image-popup-checkbox-label" for="popupImageToday">
                <input type="checkbox" id="popupImageToday" data-popup-today>
                <span class="image-popup-checkbox-box" aria-hidden="true">
                    <i class="fa-solid fa-check"></i>
                </span>
                <span>오늘 하루 보지 않기</span>
            </label>
            <button type="button" class="image-popup-close" data-popup-close>
                <span>닫기</span>
                <i class="fa-regular fa-xmark" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</div>
