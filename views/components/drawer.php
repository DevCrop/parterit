<?php
/** @var \Menu\Menu|null $menu */
$menu = app()->get('menu');
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '/';
?>
<aside id="main-drawer" class="no-drawer" data-state="closed" role="dialog" aria-modal="true" aria-label="사이트 메뉴"
    aria-hidden="true" data-lenis-prevent data-lenis-prevent-wheel data-lenis-prevent-touch>
    <div class="no-modal__close--container">
        <button type="button" class="no-modal__close" aria-label="닫기">
            <i class="fa-regular fa-xmark" aria-hidden="true"></i>
        </button>
    </div>
    <div class="no-drawer__inner">
        <div class="no-drawer__container no-container-2xl">
            <div class="no-drawer__center" data-lenis-prevent data-lenis-prevent-wheel data-lenis-prevent-touch>
                <nav class="no-drawer__nav" aria-label="사이트 메뉴">
                    <?php if ($menu): ?>
                    <ul class="no-drawer__gnb">
                        <?php foreach ($menu->rootItems() as $root):
                            if (!$root->isVisible()) {
                                continue;
                            }
                            $children = $root->children(true);
                            $rootHref = e($children ? reset($children)->url() : $root->url());
                            $rootLabel = e($root->label());
                            $rootTarget = e($root->target());
                            ?>
                        <li class="no-drawer__gnb-item<?= $root->isActive() ? ' is-active' : '' ?>">
                            <a href="<?= $rootHref ?>" target="<?= $rootTarget ?>"
                                <?php if ($root->isBlank()): ?>rel="noopener noreferrer"<?php endif; ?>
                                class="no-drawer__gnb-link<?= $root->isActive() ? ' is-active' : '' ?>"<?= $root->isActive() ? ' aria-current="location"' : '' ?>>
                                <span
                                    class="no-drawer__gnb-label f-display-3 --bold clr-text-title"><?= $rootLabel ?></span>
                            </a>
                            <?php if (!empty($children)): ?>
                            <div class="no-drawer__submenu">
                                <ul class="no-drawer__lnb">
                                    <?php foreach ($children as $child):
                                        if (!$child->isVisible()) {
                                            continue;
                                        }
                                        $childHref = e($child->url());
                                        $childLabel = e($child->label());
                                        $childTarget = e($child->target());
                                        $childPath = parse_url($childHref, PHP_URL_PATH) ?: $childHref;
                                        $isActive = ($childPath === $currentPath);
                                        ?>
                                    <li class="no-drawer__lnb-item">
                                        <a href="<?= $childHref ?>" target="<?= $childTarget ?>"
                                            <?php if ($child->isBlank()): ?>rel="noopener noreferrer"<?php endif; ?>
                                            class="no-drawer__lnb-link <?= $isActive ? 'active' : '' ?>"<?= $isActive ? ' aria-current="page"' : '' ?>>
                                            <span
                                                class="no-drawer__lnb-label f-body-1 <?= $isActive ? '--semibold clr-text-title' : '--bold clr-text-body' ?>"><?= $childLabel ?></span>
                                            <?php if ($child->isBlank()): ?>
                                            <i class="fa-regular fa-arrow-up-right no-drawer__lnb-external"
                                                aria-hidden="true"></i>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </div>
</aside>
