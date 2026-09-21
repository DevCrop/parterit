<?php
$theme = isset($theme) ? (string) $theme : 'light';
$title = isset($title) ? (string) $title : '';
$count = isset($count) ? (int)    $count : 16;

$blockClass = 'no-partners-grid no-partners-grid--' . ($theme === 'dark' ? 'dark' : 'light');
?>
<section class="<?= e($blockClass) ?>">
    <div class="no-partners-grid__inner">
        <?php if ($title !== ''): ?>
        <div class="no-container-xl">
            <h2 class="no-partners-grid__title">
                <span class="--fm-en --regular f-heading-3"><?= e($title) ?></span>
            </h2>
        </div>
        <?php endif; ?>
        <div class="no-container-xl">
            <ul class="no-partners-grid__list" aria-label="Partners">
                <?php for ($i = 1; $i <= $count; $i++): ?>
                <li class="no-partners-grid__item">
                    <?= image_picture('logo/client_logo_' . $i . '.png', [
                        'alt' => '',
                        'class' => 'no-partners-grid__logo',
                        'loading' => 'lazy',
                        'decoding' => 'async',
                    ]) ?>
                </li>
                <?php endfor; ?>
            </ul>
        </div>
    </div>
</section>