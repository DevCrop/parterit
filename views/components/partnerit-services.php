<section class="partnerit-services no-section-lg" aria-labelledby="partnerit-services-title">
    <header class="partnerit-services__header">
        <h2 class="partnerit-services__title f-heading-1 --bold" id="partnerit-services-title">Our Services</h2>
        <p class="partnerit-services__intro f-body-1">
            기업마다 다른 방향과 다른 목표,<br>
            그 차이를 이해하는 기회와 연결의 시작점
        </p>
    </header>

    <div class="partnerit-services__inner no-container-xl">
        <ol class="partnerit-services__list">
            <li class="partnerit-services__item">
                <div class="partnerit-services__copy">
                    <div class="partnerit-services__text-group">
                        <span class="partnerit-services__eyebrow f-body-3 --bold">맞춤형 공고 매칭</span>
                        <h3 class="partnerit-services__item-title f-heading-3 --bold">
                            <span>참여 요건 미충족 사업은 제외,</span>
                            <span>지원가능한 사업만 선별하여 제공</span>
                        </h3>
                        <p class="partnerit-services__description f-body-3">
            파트너잇은 기업의 업종, 기술 역량, 재무상태, 수행 이력 등을 바탕으로 지원자격을 자동 판별하여 부적합한 사업은 제외하고, 지원 가능한 공고만 매칭합니다. 사용자는 별도의 불필요한 탐색 없이 AI가 지원 가능한 공고만 선별해주는 효율적인 탐색 경험을 제공받을 수 있습니다.
                        </p>
                    </div>
                    <a class="partnerit-services__link no-button f-body-3 --bold" href="<?= e(base_path('/matching')) ?>">
                        <span>자세히 보기</span>
                        <img class="partnerit-services__arrow" src="<?= e(base_path('/resource/images/main/services/services-arrow.svg')) ?>" width="20" height="20" alt="" aria-hidden="true">
                    </a>
                </div>
                <figure class="partnerit-services__visual partnerit-services__visual--matching" role="img" aria-label="맞춤형 공고 매칭 화면 예시">
                    <img class="partnerit-services__visual-background partnerit-services__visual-background--matching-primary" src="<?= e(base_path('/resource/images/main/services/services-match-bg-1.png')) ?>" alt="" width="1926" height="1260" loading="lazy" decoding="async" aria-hidden="true">
                    <img class="partnerit-services__visual-background partnerit-services__visual-background--matching-secondary" src="<?= e(base_path('/resource/images/main/services/services-match-bg-2.png')) ?>" alt="" width="1926" height="1260" loading="lazy" decoding="async" aria-hidden="true">
                    <img class="partnerit-services__mockup partnerit-services__mockup--matching" src="<?= e(base_path('/resource/images/main/services/services-matching-ui.png')) ?>" alt="" width="1791" height="1167" loading="lazy" decoding="async">
                </figure>
            </li>

            <li class="partnerit-services__item partnerit-services__item--reverse">
                <div class="partnerit-services__copy">
                    <div class="partnerit-services__text-group">
                        <span class="partnerit-services__eyebrow f-body-3 --bold">AI 분석 리포트</span>
                        <h3 class="partnerit-services__item-title f-heading-3 --bold">
                            <span>공고 해석, 조건 분석, 판단까지,</span>
                            <span>하나로 끝내는 AI분석 리포트</span>
                        </h3>
                        <p class="partnerit-services__description f-body-3">
                            NLP 기반 키워드 추출과 임베딩 유사도 분석, 조건 매칭 로직을 통해 공고 분석 결과를 시각화된 리포트로 제공합니다. 복잡한 공고 해석 없이도 적합도, 역량분석, 가점 여부, 자격요건, 경쟁률 등 10가지의 핵심 판단 요소를 한눈에 파악할 수 있어 지원 가능성과 전략을 빠르게 수립할 수 있습니다.
                        </p>
                    </div>
                    <a class="partnerit-services__link no-button f-body-3 --bold" href="<?= e(base_path('/ai-report')) ?>">
                        <span>자세히 보기</span>
                        <img class="partnerit-services__arrow" src="<?= e(base_path('/resource/images/main/services/services-arrow.svg')) ?>" width="20" height="20" alt="" aria-hidden="true">
                    </a>
                </div>
                <figure class="partnerit-services__visual partnerit-services__visual--report" role="img" aria-label="AI 분석 리포트 화면 예시">
                    <img class="partnerit-services__visual-background partnerit-services__visual-background--report" src="<?= e(base_path('/resource/images/main/services/services-report-bg.png')) ?>" alt="" width="1926" height="1260" loading="lazy" decoding="async" aria-hidden="true">
                    <img class="partnerit-services__mockup partnerit-services__mockup--report" src="<?= e(base_path('/resource/images/main/services/services-report-ui.png')) ?>" alt="" width="1632" height="2281" loading="lazy" decoding="async">
                </figure>
            </li>

            <li class="partnerit-services__item">
                <div class="partnerit-services__copy">
                    <div class="partnerit-services__text-group">
                        <span class="partnerit-services__eyebrow f-body-3 --bold">컨소시엄 매칭</span>
                        <h3 class="partnerit-services__item-title f-heading-3 --bold">
                            <span>분석부터 연결까지,</span>
                            <span>필요한 파트너를 AI가 자동으로 매칭</span>
                        </h3>
                        <p class="partnerit-services__description f-body-3">
                            단순 키워드 필터링이 아닌, 기술 의미 기반의 역할·기업 연결 분석을 통해 컨소시엄 매칭의 정확도와 공정성을 향상시킵니다. 생성된 매칭룸을 기준으로 부족한 기술역량을 자동 판단하고 해당 역량을 보유한 기업을 추천하거나, 반대로 특정 기업에게 참여 가능한 컨소시엄 리스트를 제안하는 양방향 추천 시스템을 완성합니다.
                        </p>
                    </div>
                    <a class="partnerit-services__link no-button f-body-3 --bold" href="<?= e(base_path('/consortium')) ?>">
                        <span>자세히 보기</span>
                        <img class="partnerit-services__arrow" src="<?= e(base_path('/resource/images/main/services/services-arrow.svg')) ?>" width="20" height="20" alt="" aria-hidden="true">
                    </a>
                </div>
                <figure class="partnerit-services__visual partnerit-services__visual--consortium" role="img" aria-label="컨소시엄 매칭 화면 예시">
                    <img class="partnerit-services__visual-background partnerit-services__visual-background--consortium" src="<?= e(base_path('/resource/images/main/services/services-consortium-bg.png')) ?>" alt="" width="1926" height="1260" loading="lazy" decoding="async" aria-hidden="true">
                    <img class="partnerit-services__mockup partnerit-services__mockup--consortium" src="<?= e(base_path('/resource/images/main/services/services-consortium-ui.png')) ?>" alt="" width="1836" height="1206" loading="lazy" decoding="async">
                </figure>
            </li>
        </ol>
    </div>
</section>
