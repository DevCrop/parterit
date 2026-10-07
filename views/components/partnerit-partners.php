<?php
$partners = [
    ['image' => 'main/partners/logo-104-20261007.png', 'name' => 'HEIM VENTURE INVESTMENT'],
    ['image' => 'main/partners/logo-105-20261007.png', 'name' => 'bluepoint'],
    ['image' => 'main/partners/logo-106-20261007.png', 'name' => '경기창조경제혁신센터'],
    ['image' => 'main/partners/logo-107-20261007.png', 'name' => '부산창조경제혁신센터'],
    ['image' => 'main/partners/logo-108-20261007.png', 'name' => 'BNK 금융지주'],
    ['image' => 'main/partners/logo-109-20261007.png', 'name' => '부산테크노파크'],
    ['image' => 'main/partners/logo-110-20261007.png', 'name' => '부산경제진흥원'],
    ['image' => 'main/partners/logo-111-20261007.png', 'name' => '정보통신산업진흥원'],
    ['image' => 'main/partners/logo-112-20261007.png', 'name' => 'ENCOM'],
    ['image' => 'main/partners/logo-113-20261007.png', 'name' => '미라클 기업연구소'],
    ['image' => 'main/partners/logo-114-20261007.png', 'name' => '꿀단지'],
    ['image' => 'main/partners/logo-115-20261007.png', 'name' => '브이드림'],
    ['image' => 'main/partners/logo-116-20261007.png', 'name' => '애기야가자'],
    ['image' => 'main/partners/logo-117-20261007.png', 'name' => 'dot'],
];
?>
<section class="partnerit-partners no-section-lg" aria-labelledby="partnerit-partners-title">
    <header class="partnerit-partners__header no-container-xl">
        <h2 class="partnerit-partners__title f-heading-1 --bold" id="partnerit-partners-title">기술 자산과 파트너십으로 쌓아가는 신뢰</h2>
        <p class="partnerit-partners__intro f-body-2">수많은 기관과 기업이 파트너잇과 함께 새로운 기회를 만들어가고 있습니다.</p>
    </header>
    <ul class="partnerit-partners__list no-container-xl" aria-label="파트너사 로고">
        <?php foreach ($partners as $partner): ?>
        <li class="partnerit-partners__item">
            <?= image_picture($partner['image'], ['alt' => $partner['name'], 'class' => 'partnerit-partners__logo' . (in_array($partner['name'], ['bluepoint', '부산경제진흥원', '애기야가자'], true) ? ' partnerit-partners__logo--canvas' : ($partner['name'] === '정보통신산업진흥원' ? ' partnerit-partners__logo--compact' : ($partner['name'] === 'dot' ? ' partnerit-partners__logo--square' : ''))), 'loading' => 'lazy', 'decoding' => 'async']) ?>
        </li>
        <?php endforeach; ?>
    </ul>
</section>
