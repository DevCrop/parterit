<?php
$href = (string) ($href ?? '#');
$label = (string) ($label ?? '자세히 보기');
$openNewTab = (bool) ($newTab ?? false);
$variant = in_array(($variant ?? 'primary'), ['primary', 'secondary', 'gradient'], true)
    ? (string) $variant
    : 'primary';
?>
<a class="partnerit-cta-link partnerit-cta-link--<?= e($variant) ?> <?= $variant === 'gradient' ? 'f-body-cta-large' : 'f-body-button' ?> --semibold" href="<?= e($href) ?>"<?= $openNewTab ? ' target="_blank" rel="noopener noreferrer"' : '' ?>>
    <span class="partnerit-cta-link__label"><?= e($label) ?></span>
</a>
