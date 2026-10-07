<?php if (project_feature_enabled('content.brochure_request', false)): ?>
<dialog class="partnerit-brochure" id="partnerit-brochure" aria-labelledby="brochure-title" aria-describedby="brochure-description" data-lenis-prevent>
    <div class="partnerit-brochure__panel">
        <button class="partnerit-brochure__close" type="button" data-brochure-close aria-label="소개서 신청 닫기"><span aria-hidden="true">×</span></button>
        <header class="partnerit-brochure__heading">
            <span class="partnerit-brochure__badge f-body-4 --semibold">PARTNER IT</span>
            <h2 class="f-heading-4 --bold" id="brochure-title">서비스 소개서 다운로드</h2>
            <p class="f-body-3" id="brochure-description">간단한 정보를 남겨주시면 서비스 소개서를 다운로드할 수 있습니다.</p>
        </header>
        <form class="partnerit-brochure__form" action="<?= e(base_path('/brochure-requests')) ?>" method="post">
            <input type="hidden" name="_token" value="<?= e(\Request\BrochureRequestService::token()) ?>">
            <input type="hidden" name="submission_key" value="<?= e(bin2hex(random_bytes(16))) ?>">
            <div class="partnerit-brochure__trap" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
            <div class="partnerit-brochure__fields">
                <?= include_view('components.partnerit-form-field', ['name' => 'company', 'label' => '회사명', 'required' => true, 'autocomplete' => 'organization', 'placeholder' => '회사명을 입력해 주세요']) ?>
                <?= include_view('components.partnerit-form-field', ['name' => 'name', 'label' => '이름', 'required' => true, 'autocomplete' => 'name', 'placeholder' => '성함을 입력해 주세요']) ?>
                <?= include_view('components.partnerit-form-field', ['name' => 'phone', 'label' => '연락처', 'type' => 'tel', 'required' => true, 'maxlength' => 30, 'autocomplete' => 'tel', 'placeholder' => '010-0000-0000']) ?>
                <?= include_view('components.partnerit-form-field', ['name' => 'email', 'label' => '이메일', 'type' => 'email', 'required' => true, 'maxlength' => 190, 'autocomplete' => 'email', 'placeholder' => 'name@company.com']) ?>
                <?= include_view('components.partnerit-form-field', ['name' => 'message', 'label' => '문의사항', 'type' => 'textarea', 'wide' => true, 'placeholder' => '궁금한 점이나 도입 계획을 남겨주세요.']) ?>
            </div>
            <div class="partnerit-brochure__consent">
                <?= include_view('components.partnerit-form-field', ['name' => 'privacy_consent', 'label' => '개인정보 수집 및 이용에 동의합니다. (필수)', 'type' => 'checkbox', 'required' => true]) ?>
                <details class="partnerit-brochure__privacy f-body-4">
                    <summary>수집·이용 내용 확인</summary>
                    <p>수집 주체: 주식회사 파트너잇<br>목적: 서비스 소개서 제공 및 도입 문의 응대<br>항목: 회사명, 이름, 연락처, 이메일, 문의사항(선택)<br>보유기간: <?= e(ProjectHelper::config('brochure.retention', '')) ?><br>동의를 거부할 수 있으나 신청 및 다운로드가 제한됩니다.</p>
                    <a href="<?= e(base_path('/legal/privacy')) ?>" target="_blank" rel="noopener noreferrer">개인정보처리방침 보기 (새 창)</a>
                </details>
            </div>
            <p class="partnerit-brochure__status f-body-3" role="status" aria-live="polite" tabindex="-1"></p>
            <button class="partnerit-cta-link partnerit-cta-link--primary f-body-button --semibold" type="submit"><span class="partnerit-cta-link__label">신청하고 소개서 다운로드</span></button>
        </form>
        <div class="partnerit-brochure__success" hidden>
            <span class="partnerit-brochure__success-mark" aria-hidden="true">✓</span>
            <h3 class="f-heading-5 --bold">신청이 완료되었습니다.</h3>
            <p class="f-body-3">다운로드가 시작됩니다.<br>시작되지 않으면 아래 버튼을 눌러주세요.</p>
            <a class="partnerit-cta-link partnerit-cta-link--primary f-body-button --semibold" data-brochure-download download="partnerit-service-brief.pdf"><span>소개서 다시 다운로드</span></a>
            <button type="button" class="partnerit-cta-link partnerit-cta-link--secondary f-body-button" data-brochure-close>닫기</button>
        </div>
    </div>
</dialog>
<?php endif; ?>
