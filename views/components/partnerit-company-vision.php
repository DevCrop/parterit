<section class="partnerit-company-vision" aria-labelledby="partnerit-company-vision-title">
    <div class="partnerit-company-vision__background" aria-hidden="true"></div>
    <div class="partnerit-company-vision__photos" aria-hidden="true">
        <?php foreach (['company-founders-vision', 'company-vision-meeting-1', 'company-vision-meeting-2', 'company-vision-meeting-3'] as $index => $photo): ?>
        <div class="partnerit-company-vision__photo partnerit-company-vision__photo--<?= $index + 1 ?>">
            <?= image_picture('shared/soft-blue-v3/' . $photo . '.png', ['alt' => '', 'loading' => 'lazy', 'decoding' => 'async']) ?>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="partnerit-company-vision__inner no-container-xl">
        <header class="partnerit-company-vision__header">
            <p class="partnerit-company-vision__eyebrow f-body-3 --semibold">파트너잇이 나아가는 방향</p>
            <h2 class="partnerit-company-vision__title f-heading-2 --bold" id="partnerit-company-vision-title">
                <span>대한민국 기업 성장의</span>
                <strong>인프라를 향해</strong>
            </h2>
        </header>
        <div class="partnerit-company-vision__message">
            <p class="partnerit-company-vision__lead f-body-company-vision-lead --bold">
                기술과 기회가 만나고, 기업과 기업이 연결되는
                <span>새로운 혁신 산업 생태계를 만들어가고 있습니다.</span>
            </p>
            <p class="partnerit-company-vision__body f-body-company-hero">
                <span class="partnerit-company-vision__line"><strong>연결</strong>을 통해 문제를 해결하고, <strong>데이터</strong>로 기회를 증명하며,</span>
                <span class="partnerit-company-vision__line"><strong>기술</strong>로 세상을 바꾸는 일에 지금도 도전하고 있습니다.</span>
            </p>
            <p class="partnerit-company-vision__closing f-body-1"><span class="partnerit-company-vision__line">우리는 단기 성과를 넘어서, 대한민국 기업 성장의</span> <span class="partnerit-company-vision__line">인프라가 되는 서비스로 도약하고자 합니다.</span></p>
        </div>
    </div>
</section>
