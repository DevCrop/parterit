<?php
$legalKind = ($kind ?? 'privacy') === 'terms' ? 'terms' : 'privacy';
$documents = json_decode(file_get_contents(dirname(__DIR__, 2) . '/resource/data/legal-documents.json'), true, 512, JSON_THROW_ON_ERROR);
$document = $documents[$legalKind];
?>
<section class="partnerit-legal" aria-labelledby="legal-title">
    <div class="partnerit-legal__inner no-container-xl">
        <header class="partnerit-legal__header">
            <p class="partnerit-legal__eyebrow f-body-3 --bold">PARTNERIT</p>
            <h1 class="f-heading-announcement-hero" id="legal-title"><?= e($document['title']) ?></h1>
            <p class="partnerit-legal__date f-body-3">시행일 <?= e($document['effectiveDate']) ?></p>
        </header>
        <nav class="partnerit-legal__tabs f-body-2 --bold" aria-label="약관 및 정책">
            <a href="<?= e(route('legal.privacy')) ?>"<?= $legalKind === 'privacy' ? ' aria-current="page"' : '' ?>>개인정보처리방침</a>
            <a href="<?= e(route('legal.cookies')) ?>"<?= $legalKind === 'terms' ? ' aria-current="page"' : '' ?>>이용약관</a>
        </nav>
        <details class="partnerit-legal__toc">
            <summary class="f-body-2 --bold">목차 보기</summary>
            <nav aria-label="문서 목차"><ol class="f-body-3">
                <?php foreach ($document['sections'] as $index => $article): ?>
                <li><a href="#legal-<?= e($legalKind) ?>-<?= $index + 1 ?>"><?= e($article['title']) ?></a></li>
                <?php endforeach; ?>
            </ol></nav>
        </details>
        <div class="partnerit-legal__content">
            <?php foreach ($document['sections'] as $index => $article): ?>
            <section class="partnerit-legal__article" aria-labelledby="legal-<?= e($legalKind) ?>-<?= $index + 1 ?>">
                <h2 class="partnerit-legal__article-title f-heading-announcement-card --bold" id="legal-<?= e($legalKind) ?>-<?= $index + 1 ?>"><?= e($article['title']) ?></h2>
                <div class="partnerit-legal__body f-body-announcement-detail">
                    <?php foreach ($article['blocks'] as $block): ?>
                    <?php if ($block['type'] === 'table'): ?>
                    <table class="partnerit-legal__table">
                        <caption class="partnerit-legal__table-caption"><?= e($article['title']) ?> 관련 안내</caption>
                        <?php if ($block['headers']): ?><thead><tr><?php foreach ($block['headers'] as $cell): ?><th scope="col"><?= e($cell) ?></th><?php endforeach; ?></tr></thead><?php endif; ?>
                        <tbody><?php foreach ($block['rows'] as $row): ?><tr><?php foreach ($row as $cell): ?><td><?= e($cell) ?></td><?php endforeach; ?></tr><?php endforeach; ?></tbody>
                    </table>
                    <?php else: ?>
                    <p class="partnerit-legal__paragraph partnerit-legal__paragraph--<?= e($block['kind']) ?>"><?= e($block['text']) ?></p>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endforeach; ?>
        </div>
    </div>
</section>
