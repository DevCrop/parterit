<section class="partnerit-announcement-profile partnerit-announcement__section" aria-labelledby="announcement-profile-title">
    <div class="partnerit-announcement-profile__inner partnerit-announcement__container">
        <div class="partnerit-announcement-profile__copy partnerit-announcement__copy">
            <p class="partnerit-announcement-profile__eyebrow f-body-3 --semibold">기업정보 활용</p>
            <h2 class="partnerit-announcement-profile__title f-heading-announcement-section" id="announcement-profile-title">
                <span>기업정보는 한 번,</span>
                <span class="partnerit-announcement-profile__title-accent">활용은 더 넓게.</span>
            </h2>
            <p class="partnerit-announcement-profile__summary partnerit-announcement__description f-body-announcement-copy">입력된 기업정보를 항목별로 구조화하여 공고 매칭, AI 리포트 생성, 컨소시엄 구성에 활용합니다.</p>
            <div class="partnerit-announcement-profile__security">
                <div class="partnerit-announcement-profile__security-icon" aria-hidden="true">
                    <?= image_picture('shared/soft-blue-v3/shield.png', ['alt' => '', 'decoding' => 'async', 'loading' => 'lazy']) ?>
                </div>
                <div class="partnerit-announcement-profile__security-copy">
                    <strong class="f-heading-announcement-feature">데이터 관리 안내</strong>
                    <p class="partnerit-announcement__note f-body-announcement-meta">입력된 기업정보는 비식별화 처리 후 내부 시스템에서 저장·관리됩니다.</p>
                </div>
            </div>
        </div>
        <ol class="partnerit-announcement-profile__list" aria-label="맞춤 공고 이용 순서">
            <?php foreach ([
                ['building', '우리 기업정보 입력', '업종·기술역량·재무정보·수행이력을 입력해 추천의 기준을 만듭니다.'],
                ['search', '맞춤 공고 확인', '추천된 공고의 핵심 요약과 적합도, 마감일을 확인합니다.'],
                ['chart', 'AI 보고서로 상세 검토', '지원자격과 가점·주의사항을 살펴보고, 필요한 파트너 분야까지 확인합니다.'],
            ] as [$icon, $title, $description]): ?>
            <li class="partnerit-announcement-profile__item">
                <div class="partnerit-announcement-profile__icon"><?= image_picture('shared/soft-blue-v3/' . $icon . '.png', ['alt' => '', 'decoding' => 'async', 'loading' => 'lazy']) ?></div>
                <div class="partnerit-announcement-profile__step-copy">
                    <h3 class="f-heading-announcement-card"><?= e($title) ?></h3>
                    <p class="partnerit-announcement__description f-body-announcement-detail"><?= e($description) ?></p>
                </div>
            </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>
