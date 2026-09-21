<?php
$headline = $headline ?? ($title ?? 'About Us');
$description = $description ?? '단순한 금융상품 제안이 아닌 고객의 삶과 재무 목표를 함께 고려해 <br>더 안정적인 자산 구조를 설계합니다.';
$image = $image ?? 'sub/sub-hero.jpg';
$section_class = trim((string) ($section_class ?? ''));
$heading_level = $heading_level ?? 'h2';
?>
<section class="no-sub-visual<?= $section_class !== '' ? ' ' . e($section_class) : '' ?>">
    <div class="no-container-xl">
        <div class="no-sub-visual__inner">
            <<?= e($heading_level) ?> class="f-display-1 --bold">
                <?= e_nl2br_html($headline) ?>
            </<?= e($heading_level) ?>>
            <p class="f-body-1 --regular">
                <?= e_nl2br_html($description) ?>
            </p>
        </div>
    </div>
    <div class="no-sub-visual__img">
        <?= image_picture($image, [
            'alt' => project_brand_name(),
            'fetchpriority' => 'high',
            'decoding' => 'async',
        ]) ?>
    </div>
</section>
