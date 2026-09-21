<section class="partnerit-ai-report-capability partnerit-ai-report__section" aria-labelledby="ai-report-capability-title">
    <div class="partnerit-ai-report-capability__inner partnerit-ai-report__container no-container-xl">
        <div class="partnerit-ai-report-capability__copy">
        <h2 class="partnerit-ai-report-capability__heading f-heading-announcement-section" id="ai-report-capability-title">역량을 확인하고,<br> 필요한 파트너까지</h2>
        <div class="partnerit-ai-report-capability__cards">
            <article class="partnerit-ai-report-capability__card">
                <div class="partnerit-ai-report-capability__icon"><?= image_picture('shared/soft-blue-v3/chart.png', ['alt' => '', 'decoding' => 'async', 'loading' => 'lazy']) ?></div>
                <div class="partnerit-ai-report-capability__text"><h3 class="f-heading-ai-report-label">종합역량 분석</h3><p class="partnerit-ai-report__description f-body-announcement-copy">기업 데이터를 바탕으로<br> 우수역량과 부족역량을 분석합니다.</p></div>
            </article>
            <article class="partnerit-ai-report-capability__card">
                <div class="partnerit-ai-report-capability__icon"><?= image_picture('shared/soft-blue-v3/team.png', ['alt' => '', 'decoding' => 'async', 'loading' => 'lazy']) ?></div>
                <div class="partnerit-ai-report-capability__text"><h3 class="f-heading-ai-report-label">컨소시엄 구성 여부</h3><p class="partnerit-ai-report__description f-body-announcement-copy">구성 필요 여부를 분석하고,<br> 필요한 파트너 분야와 수를 제안합니다.</p></div>
            </article>
        </div>
        <div class="partnerit-ai-report-capability__actions">
            <?= include_view('components.partnerit-cta-link', ['href' => base_path('/consortium'), 'label' => '컨소시엄 매칭 알아보기', 'variant' => 'primary']) ?>
        </div>
        </div>
        <figure class="partnerit-ai-report-capability__visual">
            <div class="partnerit-ai-report-capability__mockup">
                <div class="partnerit-ai-report-capability__toolbar" aria-hidden="true"><span></span><span></span><span></span></div>
                <?= image_picture('business/ai-report/capability-report-3x.png', ['alt' => '종합역량, 우수역량 TOP 3, 부족역량 TOP 3 및 역량분석 결과', 'decoding' => 'async', 'loading' => 'lazy']) ?>
            </div>
        </figure>
    </div>
</section>
