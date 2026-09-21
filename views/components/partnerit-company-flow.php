<?php
$companyFlow = [
    ['icon' => 'search.png', 'title' => '탐색 · 공고 매칭', 'description' => '기업 프로필과 공고의 자격요건을 분석해, 지원 가능한 사업만 정밀하게 매칭합니다.'],
    ['icon' => 'chart.png', 'title' => '분석 · AI 보고서', 'description' => '지원자격, 우대조건, 가점요소, 준비사항을 분석해 기업의 의사결정을 돕습니다.'],
    ['icon' => 'team.png', 'title' => '연결 · 파트너 매칭', 'description' => '필요한 역할과 역량을 분석하고, 보유 기술과 사업분야를 기반으로 협력기업을 추천합니다.'],
    ['icon' => 'history.png', 'title' => '실행 · 통합 협업', 'description' => '실시간 소통부터 문서 관리, 프로젝트 진행상황 관리까지 하나의 협업 환경에서 제공합니다.'],
];
?>
<section class="partnerit-company-flow" aria-labelledby="partnerit-company-flow-title">
    <div class="partnerit-company-flow__inner no-container-xl">
        <h2 class="partnerit-company-flow__title f-heading-company-flow-title" id="partnerit-company-flow-title">
            <span>기업의 성장에 필요한 연결을</span>
            <span>하나의 플랫폼에서</span>
        </h2>
        <ol class="partnerit-company-flow__grid">
            <?php foreach ($companyFlow as $index => $item): ?>
                <li class="partnerit-company-flow__card">
                    <figure class="partnerit-company-flow__icon" aria-hidden="true">
                        <?= image_picture('shared/soft-blue-v3/' . $item['icon'], ['alt' => '', 'class' => 'partnerit-company-flow__icon-image']) ?>
                    </figure>
                    <div class="partnerit-company-flow__copy">
                        <h3 class="partnerit-company-flow__card-title f-heading-consortium-card --bold"><?= e($item['title']) ?></h3>
                        <p class="partnerit-company-flow__description f-body-2 --medium"><?= e($item['description']) ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>
