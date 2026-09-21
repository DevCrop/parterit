export class SwiperController {
  // 메인비주얼: 슬라이드별 "직전에 보였던 텍스트"를 기억 → 전환 시 즉시 끄지 않고 부드럽게 페이드아웃.
  #visualPrevTargets = new WeakMap();

  init() {
    this.#initMainVisualSwiper();
    this.#initSubMediaSwiper();
    this.#initCategorySwiper();
    this.#initPillSubnav();
    this.#initBusinessSubnav();
    this.#initContactTabSwiper();
    this.#initFeaturedStoriesSwiper();
    this.#initPartneritReviewsSwiper();
    this.#initCompanyCertificatesSwiper();
  }

  #initFeaturedStoriesSwiper() {
    const swipers = document.querySelectorAll("[data-partnerit-stories-featured-swiper]");
    if (!swipers.length || typeof window.Swiper !== "function") return;

    swipers.forEach((swiper) => {
      if (swiper.dataset.swiperInitialized === "1") return;

      const slides = swiper.querySelectorAll(".swiper-slide");
      if (slides.length < 2) return;

      const section = swiper.closest(".partnerit-stories-featured");
      const prevButton = section?.querySelector(".partnerit-stories-featured__button--prev");
      const nextButton = section?.querySelector(".partnerit-stories-featured__button--next");
      const pagination = section?.querySelector(".partnerit-stories-featured__pagination");
      if (!section || !prevButton || !nextButton || !pagination) return;

      swiper.dataset.swiperInitialized = "1";
      const reduceMotion = window.matchMedia?.("(prefers-reduced-motion: reduce)").matches ?? false;
      const featuredSwiper = new window.Swiper(swiper, {
        slidesPerView: 1,
        spaceBetween: 24,
        speed: reduceMotion ? 0 : 650,
        loop: true,
        autoplay: reduceMotion
          ? false
          : { delay: 6000, disableOnInteraction: false, pauseOnMouseEnter: true },
        keyboard: { enabled: true, onlyInViewport: true },
        navigation: { prevEl: prevButton, nextEl: nextButton },
        pagination: {
          el: pagination,
          clickable: true,
          bulletElement: "button",
          renderBullet(index, className) {
            return `<button class="${className}" type="button" aria-label="${index + 1}번째 대표 고객사례 보기"></button>`;
          },
        },
      });

      this.#bindAccessibleAutoplay(section, featuredSwiper, reduceMotion);
    });
  }

  #initCompanyCertificatesSwiper() {
    const swipers = document.querySelectorAll("[data-company-certificates-swiper]");
    if (!swipers.length || typeof window.Swiper !== "function") return;

    swipers.forEach((swiper) => {
      if (swiper.dataset.swiperInitialized === "1") return;

      const section = swiper.closest(".partnerit-company-certificates");
      const prevButton = section?.querySelector(".partnerit-company-certificates__button--prev");
      const nextButton = section?.querySelector(".partnerit-company-certificates__button--next");
      const pagination = section?.querySelector(".partnerit-company-certificates__pagination");
      if (!section || !prevButton || !nextButton || !pagination) return;

      swiper.dataset.swiperInitialized = "1";
      const reduceMotion = window.matchMedia?.("(prefers-reduced-motion: reduce)").matches ?? false;

      const certificateSwiper = new window.Swiper(swiper, {
        slidesPerView: 2.5,
        slidesPerGroup: 1,
        spaceBetween: 40,
        speed: reduceMotion ? 0 : 600,
        loop: true,
        watchOverflow: true,
        autoplay: reduceMotion
          ? false
          : { delay: 3600, disableOnInteraction: false, pauseOnMouseEnter: true },
        keyboard: { enabled: true, onlyInViewport: true },
        navigation: { prevEl: prevButton, nextEl: nextButton },
        pagination: {
          el: pagination,
          clickable: true,
          bulletElement: "button",
          renderBullet(index, className) {
            return `<button class="${className}" type="button" aria-label="인증서 ${index + 1}번째 묶음 보기"></button>`;
          },
        },
        breakpoints: {
          0: { slidesPerView: 2.5, spaceBetween: 12 },
          761: { slidesPerView: 3, spaceBetween: 24 },
          1201: { slidesPerView: 3.5, spaceBetween: 40 },
        },
      });

      this.#bindAccessibleAutoplay(section, certificateSwiper, reduceMotion);
    });
  }

  #initPartneritReviewsSwiper() {
    const swipers = document.querySelectorAll("[data-partnerit-reviews-swiper]");
    if (!swipers.length || typeof window.Swiper !== "function") return;

    swipers.forEach((swiper) => {
      if (swiper.dataset.swiperInitialized === "1") return;

      const section = swiper.closest(".partnerit-reviews");
      const prevButton = section?.querySelector(".partnerit-reviews__button--prev");
      const nextButton = section?.querySelector(".partnerit-reviews__button--next");
      const pagination = section?.querySelector(".partnerit-reviews__pagination");
      if (!section || !prevButton || !nextButton || !pagination) return;
      const reduceMotion = window.matchMedia?.("(prefers-reduced-motion: reduce)").matches ?? false;

      swiper.dataset.swiperInitialized = "1";

      const reviewSwiper = new window.Swiper(swiper, {
        slidesPerView: "auto",
        centeredSlides: true,
        spaceBetween: -92,
        speed: reduceMotion ? 0 : 650,
        loop: true,
        loopedSlides: swiper.querySelectorAll(".swiper-slide").length,
        watchSlidesProgress: true,
        slideToClickedSlide: true,
        autoplay: reduceMotion
          ? false
          : { delay: 5200, disableOnInteraction: false, pauseOnMouseEnter: true },
        keyboard: { enabled: true, onlyInViewport: true },
        navigation: { prevEl: prevButton, nextEl: nextButton },
        pagination: {
          el: pagination,
          clickable: true,
          bulletElement: "button",
          renderBullet(index, className) {
            return `<button class="${className}" type="button" aria-label="${index + 1}번째 후기 보기"></button>`;
          },
        },
        breakpoints: {
          0: { spaceBetween: -8 },
          701: { spaceBetween: -32 },
          1025: { spaceBetween: -92 },
        },
      });

      section.querySelectorAll("[data-partnerit-review-next]").forEach((button) => {
        button.addEventListener("click", () => reviewSwiper.slideNext());
      });

      this.#bindAccessibleAutoplay(section, reviewSwiper, reduceMotion);
    });
  }

  #bindAccessibleAutoplay(section, swiper, reduceMotion) {
    if (reduceMotion || !section || !swiper?.autoplay) return;

    section.addEventListener("focusin", () => swiper.autoplay.stop());
    section.addEventListener("focusout", (event) => {
      if (!section.contains(event.relatedTarget)) swiper.autoplay.start();
    });
  }

  // about(/about) · business(/business) 의 알약형 서브네브 — 동일 구성(가로 free-scroll).
  #initPillSubnav() {
    const swipers = document.querySelectorAll(".about-nav .swiper, .business-nav .swiper");
    if (!swipers.length || typeof window.Swiper !== "function") return;

    swipers.forEach((swiper) => {
      if (swiper.dataset.swiperInitialized === "1") return;
      swiper.dataset.swiperInitialized = "1";

      new window.Swiper(swiper, {
        slidesPerView: "auto",
        spaceBetween: 8,
        speed: 400,
        freeMode: { enabled: true, momentum: true },
        watchOverflow: true,
      });
    });
  }

  #initMainVisualSwiper() {
    const swipers = document.querySelectorAll(".visual-swiper");
    if (!swipers.length || typeof window.Swiper !== "function") return;

    // 모션 비선호 사용자는 active 연출 없이 텍스트를 그대로 노출(CSS 초기 숨김도
    // @media (prefers-reduced-motion: no-preference) 로 게이트되어 있어 안전).
    const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const animate = !reduceMotion && typeof window.gsap !== "undefined";

    swipers.forEach((swiper) => {
      if (swiper.dataset.swiperInitialized === "1") return;

      const section = swiper.closest(".main-visual");
      const prevButton = section?.querySelector(".visual-prev");
      const nextButton = section?.querySelector(".visual-next");
      const pagination = section?.querySelector(".visual-pagination");

      swiper.dataset.swiperInitialized = "1";

      // fail-safe: "gsap 사용 가능 + 모션 허용" 일 때만 텍스트 초기 숨김을 켠다.
      // 이 클래스가 없으면 CSS 가 숨기지 않으므로 컨트롤러가 못 돌거나 gsap 부재 시
      // 히어로 텍스트가 빈 채로 남지 않는다.
      if (animate && section) section.classList.add("is-visual-armed");

      new window.Swiper(swiper, {
        slidesPerView: 1,
        speed: 1500,
        loop: true,
        watchOverflow: true,
        autoplay: { delay: 6000, disableOnInteraction: false },
        navigation: prevButton && nextButton
          ? { prevEl: prevButton, nextEl: nextButton }
          : undefined,
        pagination: pagination
          ? { el: pagination, clickable: true }
          : undefined,
        on: animate
          ? {
              afterInit: (sw) => this.#revealActiveVisualSlide(sw),
              slideChangeTransitionStart: (sw) => this.#revealActiveVisualSlide(sw),
            }
          : undefined,
      });
    });
  }

  // active 슬라이드의 .title·.txt 를 GSAP 으로 fade-up.
  // loop:true 라 슬라이드가 복제되므로, 현재 보이는 슬라이드(.swiper-slide-active)
  // 기준으로 타깃을 잡고 나머지 슬라이드(복제 포함)는 다음 차례를 위해 숨겨 둔다.
  #revealActiveVisualSlide(swiper) {
    if (typeof window.gsap === "undefined") return;

    const textOf = (slide) =>
      slide instanceof HTMLElement ? Array.from(slide.querySelectorAll(".title, .txt")) : [];

    const active =
      swiper.el.querySelector(".swiper-slide-active") || swiper.slides[swiper.activeIndex];
    const targets = textOf(active);

    // 막 떠나는(직전 active) 슬라이드 텍스트는 즉시 끄지 말고 부드럽게 페이드아웃 —
    // 슬라이드가 전환 동안 화면에 보이는데 글자만 툭 사라지는 현상 방지.
    const outgoing = (this.#visualPrevTargets.get(swiper) || []).filter((el) => !targets.includes(el));
    if (outgoing.length) {
      gsap.to(outgoing, { autoAlpha: 0, duration: 0.5, ease: "power2.in", overwrite: true });
    }

    // 화면 밖 나머지 슬라이드는 즉시 숨겨 다음 등장 대비(보이지 않으므로 점프 없음).
    swiper.slides.forEach((slide) => {
      if (slide === active) return;
      const els = textOf(slide).filter((el) => !outgoing.includes(el));
      if (els.length) gsap.set(els, { autoAlpha: 0, y: 32 });
    });

    if (!targets.length) {
      this.#visualPrevTargets.set(swiper, []);
      return;
    }

    gsap.set(targets, { autoAlpha: 0, y: 32 });
    gsap.to(targets, {
      autoAlpha: 1,
      y: 0,
      duration: 0.9,
      ease: "power3.out",
      stagger: 0.12,
      overwrite: true,
    });
    this.#visualPrevTargets.set(swiper, targets);
  }

  #initSubMediaSwiper() {
    const swipers = document.querySelectorAll(".no-sub-media__swiper");
    if (!swipers.length || typeof window.Swiper !== "function") return;

    swipers.forEach((swiper) => {
      if (swiper.dataset.swiperInitialized === "1") return;

      const section = swiper.closest(".no-sub-media");
      const prevButton = section?.querySelector(".no-sub-media__button--prev");
      const nextButton = section?.querySelector(".no-sub-media__button--next");

      swiper.dataset.swiperInitialized = "1";

      new window.Swiper(swiper, {
        slidesPerView: 1.08,
        spaceBetween: 16,
        speed: 600,
        watchOverflow: true,
        navigation: prevButton && nextButton
          ? { prevEl: prevButton, nextEl: nextButton }
          : undefined,
        breakpoints: {
          768: { slidesPerView: 2.2, spaceBetween: 20 },
          1025: { slidesPerView: 3, spaceBetween: 24 },
        },
      });
    });
  }

  #initCategorySwiper() {
    const swipers = document.querySelectorAll(".no-category__swiper");
    if (!swipers.length || typeof window.Swiper !== "function") return;

    swipers.forEach((swiper) => {
      if (swiper.dataset.swiperInitialized === "1") return;
      swiper.dataset.swiperInitialized = "1";

      new window.Swiper(swiper, {
        slidesPerView: "auto",
        spaceBetween: 8,
        speed: 680,
        slideToClickedSlide: true,
        freeMode: { enabled: true, momentum: true },
        watchOverflow: true,
        breakpoints: {
          768: { spaceBetween: 10 },
          1025: { spaceBetween: 12 },
        },
      });
    });
  }

  #initBusinessSubnav() {
    const swipers = document.querySelectorAll(".no-business-subnav__swiper");
    if (!swipers.length || typeof window.Swiper !== "function") return;

    swipers.forEach((swiper) => {
      if (swiper.dataset.swiperInitialized === "1") return;
      swiper.dataset.swiperInitialized = "1";

      new window.Swiper(swiper, {
        slidesPerView: "auto",
        spaceBetween: 8,
        speed: 400,
        freeMode: { enabled: true, momentum: true },
        watchOverflow: true,
      });
    });

    const header = document.querySelector(".no-header");
    const subnav = document.querySelector(".no-business-subnav");
    if (!header || !subnav) return;

    new MutationObserver(() => {
      subnav.style.top = header.classList.contains("is-hidden") ? "0px" : "";
    }).observe(header, { attributes: true, attributeFilter: ["class"] });
  }

  #initContactTabSwiper() {
    const swipers = document.querySelectorAll(".no-career-contact__tab-swiper");
    if (!swipers.length || typeof window.Swiper !== "function") return;

    swipers.forEach((swiper) => {
      if (swiper.dataset.swiperInitialized === "1") return;
      swiper.dataset.swiperInitialized = "1";

      new window.Swiper(swiper, {
        slidesPerView: "auto",
        spaceBetween: 8,
        speed: 400,
        freeMode: { enabled: true, momentum: true },
        watchOverflow: true,
      });
    });
  }
}
