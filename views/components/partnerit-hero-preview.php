<div class="partnerit-hero__visual" role="img" aria-label="기업 맞춤 공고 추천, AI 분석 리포트와 컨소시엄 파트너 추천 서비스 화면 예시">
    <div class="partnerit-hero__visual-item partnerit-hero__visual-item--browser" aria-hidden="true">
        <div class="partnerit-hero__laptop"><img src="<?= e(base_path('/resource/images/main/hero/laptop-original-20261007.png')) ?>" alt="" decoding="async" fetchpriority="high"></div>
        <div class="partnerit-hero__screen">
            <div class="partnerit-hero__toolbar"><span>● ● ●</span><span>partnerit.kr</span><span>＋</span></div>
            <div class="partnerit-hero__dashboard">
                <div class="partnerit-hero__announcements">
                    <header><strong>추천된 공고</strong><p>우리 기업에 적합한 정부지원사업을 확인하세요.</p></header>
                    <div class="partnerit-hero__recommendations">
                        <?php foreach (['AI 스마트 제조 혁신', 'AI 데이터 분석 시스템', '공공 클라우드 플랫폼'] as $i => $title): ?>
                        <div class="partnerit-hero__recommendation"><small>중소벤처기업부 · D-<?= 5 + $i ?></small><strong><?= e($title) ?></strong><span>AI 추천 <?= 97 - $i * 3 ?>%</span></div>
                        <?php endforeach; ?>
                    </div>
                    <strong class="partnerit-hero__list-label">AI 매칭 공고</strong>
                    <?php foreach (['AI 반도체 최적화 설계 지원 사업', '디지털 헬스케어 시스템 개발 및 실증', '디지털 역량강화 지원사업'] as $i => $title): ?>
                    <div class="partnerit-hero__announcement"><small>정부지원사업 · D-<?= 10 - $i ?></small><strong><?= e($title) ?></strong><p>기업정보와 보유 기술을 기반으로 분석한 맞춤 공고입니다.</p><span>AI 추천 <?= 88 - $i * 2 ?>%</span></div>
                    <?php endforeach; ?>
                </div>
                <aside class="partnerit-hero__analysis"><small>공고 상세</small><strong>디지털 헬스케어<br>시스템 개발 및 실증</strong><div><b>공고 요약</b><p>디지털 기술 기반 헬스케어 솔루션 개발 및 실증 지원</p></div><div><b>공고 분석</b><p>사업기간 · 지원규모 · 참여자격</p></div><div><b>종합 분석</b><span class="partnerit-hero__preview-score">87%</span><p>보유 기술과 사업분야를 함께 분석합니다.</p></div><div class="partnerit-hero__analysis-note">필요한 파트너도<br>함께 확인해 보세요.</div></aside>
            </div>
        </div>
    </div>
    <div class="partnerit-hero__visual-item partnerit-hero__visual-item--mail partnerit-hero__preview-card" aria-hidden="true">
        <header><span class="partnerit-hero__preview-icon">✉</span><div><small>맞춤 공고 메일</small><strong>새로운 추천 3건</strong></div><b>3</b></header>
        <ul class="partnerit-hero__mail-list">
            <?php foreach (['AI 제조혁신 지원사업', '스마트공장 고도화 사업', '데이터 활용 바우처'] as $i => $title): ?>
            <li><strong><?= e($title) ?></strong><small>적합도 <?= 94 - $i * 3 ?>% · D-<?= 7 + $i * 5 ?></small></li>
            <?php endforeach; ?>
        </ul>
        <span class="partnerit-hero__preview-button">맞춤 공고 메일 열기</span>
    </div>
    <div class="partnerit-hero__visual-item partnerit-hero__visual-item--report partnerit-hero__preview-card" aria-hidden="true">
        <header><div><small>AI 분석 완료 · 방금 전</small><strong>공고 분석 리포트</strong></div><b class="partnerit-hero__report-score">89</b></header>
        <p>핵심 역량과 지원 가능성을 확인하세요.</p>
        <div class="partnerit-hero__report-values"><span><small>기술 일치</small><strong>92%</strong></span><span><small>선정 예상</small><strong>높음</strong></span><span><small>보완 항목</small><strong>1건</strong></span></div>
        <span class="partnerit-hero__preview-button">AI 리포트 바로가기 →</span>
    </div>
    <img class="partnerit-hero__visual-item partnerit-hero__visual-item--match" src="<?= e(base_path('/resource/images/main/hero/consortium-match-260916.svg')) ?>" alt="" aria-hidden="true" width="323" height="258">
</div>
