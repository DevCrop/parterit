<?php
$companyHistory = [
    '2026' => [
        '제19기 Start-up NEST 최종 선정',
        '기술보증기금 지방청년창업 성공 프로그램 최종 선정',
        '모두의 창업 AI 솔루션 공급기업 적정 판정',
        '청년창업사관학교 16기 최종 합격',
        '한국저작권위원회 소프트웨어 저작권 등록',
    ],
    '2025' => [
        '부산광역시 공공 빅데이터 활용 경진대회 우수상',
        '가중 적합도 기반 사업 공고 자동 매칭 알고리즘 특허 출원',
        '한국산업기술진흥협회(KOITA) 회원사 가입',
        '하임벤처투자 파트너십 계약 체결',
        '연구개발전담부서 설립',
        '‘파트너잇’ 상표권 출원',
        '주식회사 파트너잇 설립',
    ],
];
?>
<section class="partnerit-company-history" aria-labelledby="partnerit-company-history-title">
    <div class="partnerit-company-history__inner no-container-xl">
        <h2 class="partnerit-company-history__title f-heading-company-section-title --bold" id="partnerit-company-history-title">파트너잇이 걸어온 길</h2>
        <div class="partnerit-company-history__years">
            <?php foreach ($companyHistory as $year => $items): ?>
                <section class="partnerit-company-history__year" aria-labelledby="company-history-<?= e($year) ?>">
                    <h3 class="partnerit-company-history__year-label f-heading-company-year --bold" id="company-history-<?= e($year) ?>"><?= e($year) ?></h3>
                    <ul class="partnerit-company-history__list">
                        <?php foreach ($items as $item): ?>
                            <li class="partnerit-company-history__item f-body-company-history"><?= e($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endforeach; ?>
        </div>
    </div>
</section>
