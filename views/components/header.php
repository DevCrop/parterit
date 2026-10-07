<?php
/** @var \Menu\Menu|null $menu */
$menu = app()->get('menu');
$headerCurrentPath = rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/') ?: '/';
?>
<header id="main-header" class="no-header partnerit-header">
    <div class="no-header__inner partnerit-header__inner">
        <div class="no-container-xl partnerit-header__bar">
            <a href="<?= e(base_path('/')) ?>" class="partnerit-header__logo" aria-label="파트너잇 홈">
                <img src="<?= e(base_path('/resource/images/logo/partnerit-header.svg')) ?>" width="169" height="34" alt="PARTNER IT">
            </a>

            <nav class="partnerit-header__nav" aria-label="기본 탐색">
                <ul class="partnerit-header__gnb">
                    <?php if ($menu): foreach ($menu->rootItems() as $root):
                        if (!$root->isVisible()) continue;
                        $children = $root->children(true);
                        $rootUrl = $children ? reset($children)->url() : $root->url();
                        ?>
                    <li class="partnerit-header__item<?= $children ? ' partnerit-header__item--has-children' : '' ?><?= $root->isActive() ? ' is-active' : '' ?>">
                        <a href="<?= e($rootUrl) ?>"<?= $children ? ' aria-haspopup="true"' : '' ?><?= $root->isActive() ? ($children ? ' aria-current="location"' : ' aria-current="page"') : '' ?>><?= e($root->label()) ?><?php if ($children): ?><span class="partnerit-header__chevron" aria-hidden="true"></span><?php endif; ?></a>
                        <?php if ($children): ?>
                        <ul class="partnerit-header__dropdown">
                            <?php foreach ($children as $child):
                                if (!$child->isVisible()) continue;
                                $childUrl = $child->url();
                                $childPath = rtrim(parse_url($childUrl, PHP_URL_PATH) ?: '/', '/') ?: '/';
                                $childIsInternal = preg_match('#^(?:[a-z][a-z0-9+.\-]*:|//)#i', $childUrl) !== 1;
                                $childIsActive = $child->isActive() || ($childIsInternal && $childPath === $headerCurrentPath);
                                ?>
                            <li><a href="<?= e($childUrl) ?>"<?= $childIsActive ? ' class="is-active" aria-current="page"' : '' ?>><?= e($child->label()) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; endif; ?>
                </ul>
            </nav>

            <div class="partnerit-header__actions">
                <a class="partnerit-header__cta" href="https://app.partnerit.kr/account/signin" target="_blank" rel="noopener noreferrer">무료 체험</a>
                <button type="button" class="partnerit-header__mobile no-header__toggle" aria-label="사이트 메뉴 열기" aria-expanded="false" aria-controls="main-drawer">
                    <i class="fa-regular fa-bars" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>
</header>
