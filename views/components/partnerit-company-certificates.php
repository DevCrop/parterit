<?php
$companyCertificates = [
    ['image' => 'certificate-patent.jpg', 'title' => '특허 출원 서류', 'status' => '특허 출원'],
    ['image' => 'certificate-trademark.jpg', 'title' => '파트너잇 상표 출원 서류', 'status' => '상표 출원'],
    ['image' => 'certificate-small-business.jpg', 'title' => '중소기업 확인서', 'status' => '2025.10'],
    ['image' => 'certificate-startup.jpg', 'title' => '창업기업 확인서', 'status' => '2025.10'],
    ['image' => 'certificate-rnd.jpg', 'title' => '연구개발전담부서 인정서', 'status' => '2025.11'],
];
?>
<section class="partnerit-company-certificates" aria-labelledby="partnerit-company-certificates-title">
    <div class="partnerit-company-certificates__inner no-container-xl">
        <h2 class="partnerit-company-certificates__title f-heading-company-section-title --bold" id="partnerit-company-certificates-title">기술자산과 기업 인증</h2>
        <div class="partnerit-company-certificates__swiper swiper" data-company-certificates-swiper>
            <ul class="partnerit-company-certificates__list swiper-wrapper">
                <?php foreach ($companyCertificates as $certificate): ?>
                    <li class="partnerit-company-certificates__slide swiper-slide">
                        <figure class="partnerit-company-certificates__document">
                            <div class="partnerit-company-certificates__media">
                                <?= image_picture('about/company/' . $certificate['image'], [
                                    'alt' => $certificate['title'],
                                    'loading' => 'lazy',
                                    'class' => 'partnerit-company-certificates__image',
                                ]) ?>
                            </div>
                            <figcaption class="partnerit-company-certificates__caption">
                                <strong class="partnerit-company-certificates__name f-heading-company-document-title"><?= e($certificate['title']) ?></strong>
                                <span class="partnerit-company-certificates__status f-body-company-document-meta"><?= e($certificate['status']) ?></span>
                            </figcaption>
                        </figure>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="partnerit-company-certificates__controls partnerit-swiper-controls">
            <button class="partnerit-company-certificates__button partnerit-company-certificates__button--prev partnerit-swiper-controls__button partnerit-swiper-controls__button--prev" type="button" aria-label="이전 인증서">
                <img src="<?= e(img_path('about/company/arrow-next.svg')) ?>" alt="" aria-hidden="true">
            </button>
            <div class="partnerit-company-certificates__pagination partnerit-swiper-controls__pagination" aria-label="인증서 페이지"></div>
            <button class="partnerit-company-certificates__button partnerit-company-certificates__button--next partnerit-swiper-controls__button partnerit-swiper-controls__button--next" type="button" aria-label="다음 인증서">
                <img src="<?= e(img_path('about/company/arrow-prev.svg')) ?>" alt="" aria-hidden="true">
            </button>
        </div>
    </div>
</section>
