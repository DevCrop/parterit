<section class="partnerit-ai-report-context partnerit-ai-report__section" aria-labelledby="ai-report-context-title">
    <div class="partnerit-ai-report-context__inner partnerit-ai-report__container no-container-xl">
        <div class="partnerit-ai-report-context__intro">
        <div class="partnerit-ai-report-context__heading">
            <p class="partnerit-ai-report__eyebrow f-body-3 --semibold">기업별 맞춤 분석</p>
            <h2 class="f-heading-announcement-section" id="ai-report-context-title">공고를 읽는 것과<br> 지원 판단은 다르니까</h2>
            <p class="partnerit-ai-report__description f-body-announcement-copy">분석에 필요한 시간과 인력은 부족하고,<br> 우리 기업에 맞는 사업인지 판단하기는 어렵습니다.<br> 파트너잇은 공고 요구조건과 기업정보를 함께 분석합니다.</p>
        </div>
        <div class="partnerit-ai-report-context__grid">
            <div class="partnerit-ai-report-context__panel-header">
                <span class="partnerit-ai-report-context__panel-title f-body-3 --semibold">기업 맞춤 분석</span>
                <span class="partnerit-ai-report-context__panel-meta f-body-4">분석 기준</span>
            </div>
            <div class="partnerit-ai-report-context__inputs">
            <article class="partnerit-ai-report-context__card">
                <header class="partnerit-ai-report-context__card-header">
                <div class="partnerit-ai-report-context__icon"><?= image_picture('shared/soft-blue-v3/document.png', ['alt' => '', 'loading' => 'lazy']) ?></div>
                <h3 class="f-body-2 --bold">공고 요구조건</h3>
                </header>
                <ul class="partnerit-ai-report-context__fields f-body-3">
                    <?php foreach (['참여자격', '제약조건', '가점요건'] as $field): ?>
                    <li><span><?= e($field) ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </article>
            <article class="partnerit-ai-report-context__card">
                <header class="partnerit-ai-report-context__card-header">
                <div class="partnerit-ai-report-context__icon"><?= image_picture('shared/soft-blue-v3/building.png', ['alt' => '', 'loading' => 'lazy']) ?></div>
                <h3 class="f-body-2 --bold">우리 기업정보</h3>
                </header>
                <ul class="partnerit-ai-report-context__fields f-body-3">
                    <?php foreach (['업종', '기술역량', '재무정보', '수행이력'] as $field): ?>
                    <li><span><?= e($field) ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </article>
            </div>
            <div class="partnerit-ai-report-context__merge" aria-hidden="true"><span class="f-body-4 --semibold">AI 분석</span></div>
            <p class="partnerit-ai-report-context__result f-body-2 --bold"><span>두 정보를 함께 분석해,</span><strong>우리 기업의 지원 판단을 돕습니다.</strong></p>
        </div>
        </div>
        <div class="partnerit-ai-report-context__walkthrough">
            <figure class="partnerit-ai-report-context__preview">
                <div class="partnerit-ai-report-context__preview-stage">
                <div class="partnerit-ai-report-context__preview-window">
                    <div class="partnerit-ai-report-context__preview-toolbar" aria-hidden="true"><span></span><span></span><span></span></div>
                <?= image_picture('business/ai-report/diagnosis-report-3x.png', ['alt' => 'AI 보고서의 종합 판단 지표 예시 화면', 'decoding' => 'async', 'loading' => 'lazy']) ?>
                <?= image_picture('business/ai-report/capability-report-3x.png', ['alt' => '이어지는 종합역량 및 우수·부족 역량 분석 화면', 'decoding' => 'async', 'loading' => 'lazy']) ?>
                </div>
                </div>
            </figure>
            <div class="partnerit-ai-report-context__reading">
                <h3 class="f-heading-announcement-section">보고서에서 확인할 세 가지</h3>
                <ol class="partnerit-ai-report-context__reading-list">
                    <?php foreach ([
                        ['01', '지원할 수 있는 사업인가요?', '신청자격과 제약조건을 먼저 확인하고, 적합도와 사업 권장도를 함께 살펴보세요.'],
                        ['02', '무엇을 준비해야 하나요?', '가점조건과 지원 시 주의사항을 확인해 놓치기 쉬운 준비 항목을 점검하세요.'],
                        ['03', '어떤 역량을 보완해야 하나요?', '우리 기업의 우수·부족 역량을 살펴보고, 컨소시엄 구성 필요 여부를 확인하세요.'],
                    ] as [$number, $title, $description]): ?>
                    <li class="partnerit-ai-report-context__reading-item">
                        <span class="partnerit-ai-report-context__number f-body-3 --bold" aria-hidden="true"><?= e($number) ?></span>
                        <div><h4 class="f-heading-ai-report-label"><?= e($title) ?></h4><p class="partnerit-ai-report__description f-body-announcement-detail"><?= e($description) ?></p></div>
                    </li>
                    <?php endforeach; ?>
                </ol>
                <p class="partnerit-ai-report-context__note f-body-announcement-meta">적합도는 기업정보와 공고의 부합 정도를 나타내며, 선정 확률을 의미하지 않습니다. 최종 신청 전 공고 원문의 자격요건을 확인해 주세요.</p>
            </div>
        </div>
    </div>
</section>
