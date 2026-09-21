<section class="partnerit-announcement-report partnerit-announcement__section" aria-labelledby="announcement-report-title">
    <div class="partnerit-announcement-report__inner partnerit-announcement__container">
        <div class="partnerit-announcement__copy">
            <h2 class="f-heading-announcement-section" id="announcement-report-title">공고를 찾는 것을 넘어,<br> 기업의 가능성까지 분석합니다</h2>
            <p class="partnerit-announcement__description f-body-announcement-copy">지원자격, 우대조건, 가점요소, 준비사항을 분석해 빠른 의사결정을 돕는 맞춤형 리포트를 제공합니다.</p>
            <p class="partnerit-announcement__description f-body-announcement-detail">컨소시엄 구성 여부와 필요한 파트너 분야도 함께 확인할 수 있습니다.</p>
            <?= include_view('components.partnerit-cta-link', ['href' => base_path('/ai-report'), 'label' => 'AI 보고서 자세히 보기', 'variant' => 'primary']) ?>
        </div>
        <figure class="partnerit-announcement__mockup"><?= image_picture('business/announcement/report-dashboard.png', ['alt' => '기업 지원 적합도, 평가 점수, 핵심 역량과 준비사항을 분석한 AI 보고서', 'decoding' => 'async', 'loading' => 'lazy']) ?></figure>
    </div>
</section>
