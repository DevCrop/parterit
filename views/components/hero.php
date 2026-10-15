<section class="partnerit-hero" aria-labelledby="partnerit-hero-title">
    <div class="partnerit-hero__inner no-container-xl">
        <div class="partnerit-hero__copy">
            <p class="partnerit-hero__eyebrow">중소기업 · 스타트업을 위한 AI 기반 통합 솔루션</p>
            <h1 class="partnerit-hero__title" id="partnerit-hero-title">
                기업의 기회를 발견하고,<br>
                분석하고, 연결합니다.
            </h1>
            <div class="partnerit-hero__actions">
                <a class="partnerit-hero__action partnerit-hero__action--primary" href="https://app.partnerit.kr/account/signin" target="_blank" rel="noopener noreferrer">
                    무료로 체험하기
                </a>
                <a class="partnerit-hero__action partnerit-hero__action--secondary" href="<?= e(base_path('/resource/files/partnerit-service-brief.pdf')) ?>" download="partnerit-service-brief.pdf"<?= project_feature_enabled('content.brochure_request', false) ? ' data-brochure-open aria-haspopup="dialog" aria-controls="partnerit-brochure"' : '' ?>>
                    서비스 소개서 다운로드
                </a>
            </div>
        </div>

        <?= include_view('components.partnerit-hero-preview') ?>
    </div>
</section>
