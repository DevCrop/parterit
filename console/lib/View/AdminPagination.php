<?php
$prevDisabled = ($listCurPage <= 1);
$nextDisabled = ($listCurPage >= $Page);

$maxVisiblePages = 5;

if ($Page <= $maxVisiblePages) {
    $startPage = 1;
    $endPage = $Page;
} else {
    if ($listCurPage <= 3) {
        $startPage = 1;
        $endPage = 5;
    } elseif ($listCurPage >= $Page - 2) {
        $startPage = $Page - 4;
        $endPage = $Page;
    } else {
        $startPage = $listCurPage - 2;
        $endPage = $listCurPage + 2;
    }
}
?>
<?php if ($Page > 0): ?>
<div class="no-pagination">
    <ul class="no-page-list">
        <li class="no-page-item <?= $prevDisabled ? 'disabled' : '' ?>">
            <?php if (!$prevDisabled): ?>
            <a href="javascript:void(0);" class="no-page-link"
                onClick="goListMove(1, '<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>');"
                title="맨 처음 페이지로 이동">
                <i class="bx bx-chevrons-left"></i>
            </a>
            <?php else: ?>
            <a href="javascript:void(0);" class="no-page-link" aria-disabled="true" title="첫 페이지 이동 불가">
                <i class="bx bx-chevrons-left"></i>
            </a>
            <?php endif; ?>
        </li>

        <li class="no-page-item <?= $prevDisabled ? 'disabled' : '' ?>">
            <?php if (!$prevDisabled): ?>
            <a href="javascript:void(0);" class="no-page-link"
                onClick="goListMove(<?= $listCurPage - 1 ?>, '<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>');"
                title="이전 페이지">
                <i class="bx bx-chevron-left"></i>
            </a>
            <?php else: ?>
            <a href="javascript:void(0);" class="no-page-link" aria-disabled="true" title="이전 페이지 이동 불가">
                <i class="bx bx-chevron-left"></i>
            </a>
            <?php endif; ?>
        </li>

        <?php for ($x = $startPage; $x <= $endPage; $x++): ?>
        <li class="no-page-item <?= $x == $listCurPage ? 'active' : '' ?>">
            <?php if ($x == $listCurPage): ?>
            <a href="javascript:void(0);" class="no-page-link active"><?= $x ?></a>
            <?php else: ?>
            <a href="javascript:void(0);"
                onClick="goListMove(<?= $x ?>, '<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>');"
                class="no-page-link"><?= $x ?></a>
            <?php endif; ?>
        </li>
        <?php endfor; ?>

        <?php if ($endPage < $Page): ?>
        <li class="no-page-item disabled"><a href="javascript:void(0);" class="no-page-link">...</a></li>
        <li class="no-page-item <?= ($Page == $listCurPage) ? 'active' : '' ?>">
            <?php if ($Page == $listCurPage): ?>
            <a href="javascript:void(0);" class="no-page-link active"><?= $Page ?></a>
            <?php else: ?>
            <a href="javascript:void(0);"
                onClick="goListMove(<?= $Page ?>, '<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>');"
                class="no-page-link"><?= $Page ?></a>
            <?php endif; ?>
        </li>
        <?php endif; ?>

        <li class="no-page-item <?= $nextDisabled ? 'disabled' : '' ?>">
            <?php if (!$nextDisabled): ?>
            <a href="javascript:void(0);" class="no-page-link"
                onClick="goListMove(<?= $listCurPage + 1 ?>, '<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>');"
                title="다음 페이지">
                <i class="bx bx-chevron-right"></i>
            </a>
            <?php else: ?>
            <a href="javascript:void(0);" class="no-page-link" aria-disabled="true" title="다음 페이지 이동 불가">
                <i class="bx bx-chevron-right"></i>
            </a>
            <?php endif; ?>
        </li>

        <li class="no-page-item <?= $nextDisabled ? 'disabled' : '' ?>">
            <?php if (!$nextDisabled): ?>
            <a href="javascript:void(0);" class="no-page-link"
                onClick="goListMove(<?= $Page ?>, '<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>');"
                title="맨 마지막 페이지로 이동">
                <i class="bx bx-chevrons-right"></i>
            </a>
            <?php else: ?>
            <a href="javascript:void(0);" class="no-page-link" aria-disabled="true" title="마지막 페이지 이동 불가">
                <i class="bx bx-chevrons-right"></i>
            </a>
            <?php endif; ?>
        </li>
    </ul>
</div>
<?php endif; ?>

<style>
.no-page-item.disabled a {
    background-color: #f5f5f5;
    color: #aaa;
    border-color: #ddd;
}

.no-page-item a svg {
    width: 16px;
    height: 16px;
    fill: currentColor;
}
</style>

<script>
function goListMove(start, url) {
    const form = document.getElementById('frm');
    const existingPageInput = form.querySelector('input[name="page"]');
    if (existingPageInput) {
        existingPageInput.remove();
    }

    const pageInput = document.createElement('input');
    pageInput.type = 'hidden';
    pageInput.name = 'page';
    pageInput.value = start;
    form.appendChild(pageInput);

    form.method = 'GET';
    form.setAttribute('action', url);
    form.submit();
}
</script>
