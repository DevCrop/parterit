export class RevealAnimator {
  static #MAIN_ARCHITECTURE_REVEAL_START = "top 66%";
  static #MAIN_TITLE_REVEAL_START = "top 70%";
  static #MAIN_CASE_CARD_ACCENT_START = "top 82%";
  static #ABOUT_SECTION_REVEAL_START = "top 74%";
  static #SUB_VISUAL_REVEAL_START = "top 78%";
  static #SUB_SECTION_REVEAL_START = "top 76%";

  init() {
    this.#initContactBannerParallax();
    this.#initContactBannerReveal();
    this.#initFaqSectionReveal();
    this.#initApplyFormReveal();
  }

  // ─── Public utilities (used by page-level scripts) ───

  bindSectionReveal({
    section,
    boundKey,
    start = RevealAnimator.#SUB_SECTION_REVEAL_START,
    selectors = [],
    resolveTargets,
    stagger = 0.12,
    duration = 0.72,
    ease = "power3.out",
    revealedClass = "is-revealed",
  }) {
    if (!(section instanceof HTMLElement)) return;

    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
      section.classList.add(revealedClass);
      return;
    }

    if (section.dataset[boundKey] === "1") return;

    const targets = typeof resolveTargets === "function"
      ? resolveTargets(section)
      : this.#collectRevealTargets(section, selectors);

    const filteredTargets = Array.from(targets).filter(
      (el) => el instanceof HTMLElement && !el.hidden,
    );

    if (!filteredTargets.length) {
      section.classList.add(revealedClass);
      return;
    }

    section.dataset[boundKey] = "1";

    this.#createTriggerOnEnter({
      trigger: section,
      start,
      onEnter: () => {
        gsap.to(filteredTargets, {
          autoAlpha: 1,
          y: 0,
          duration,
          ease,
          stagger,
          onComplete: () => section.classList.add(revealedClass),
        });
      },
    });
  }

  splitTextToChars(element, className) {
    if (!(element instanceof HTMLElement)) return [];

    if (element.dataset.charSplitReady === "1") {
      return Array.from(element.querySelectorAll(`.${className}`));
    }

    const fragment = document.createDocumentFragment();
    const lines = [];
    let currentLine = "";

    const appendChars = (value) => {
      Array.from(value).forEach((char) => {
        const wrap = document.createElement("span");
        const span = document.createElement("span");
        wrap.className = `${className}-wrap`;
        span.className = className;
        span.setAttribute("aria-hidden", "true");
        span.textContent = char === " " ? " " : char;
        wrap.appendChild(span);
        fragment.appendChild(wrap);
      });
    };

    Array.from(element.childNodes).forEach((node) => {
      if (node.nodeType === Node.TEXT_NODE) { currentLine += node.textContent ?? ""; return; }
      if (node.nodeType === Node.ELEMENT_NODE && node.nodeName === "BR") {
        lines.push(currentLine); currentLine = ""; return;
      }
      currentLine += node.textContent ?? "";
    });
    lines.push(currentLine);

    const normalizedLines = lines.map((l) => l.replace(/\s+/g, " ").trim()).filter(Boolean);
    normalizedLines.forEach((line, i) => {
      appendChars(line);
      if (i < normalizedLines.length - 1) fragment.appendChild(document.createElement("br"));
    });

    element.setAttribute("aria-label", normalizedLines.join(" "));
    element.replaceChildren(fragment);
    element.dataset.charSplitReady = "1";

    return Array.from(element.querySelectorAll(`.${className}`));
  }

  prepareLayeredTextFill(element, baseClass, fillClass) {
    if (!(element instanceof HTMLElement)) return null;
    if (element.dataset.layeredFillReady === "1") return element.querySelector(`.${fillClass}`);

    const sourceHtml = element.innerHTML.trim();
    const text = (element.textContent ?? "").replace(/\s+/g, " ").trim();
    const base = document.createElement("span");
    const fill = document.createElement("span");

    base.className = baseClass;
    fill.className = fillClass;
    base.setAttribute("aria-hidden", "true");
    fill.setAttribute("aria-hidden", "true");
    base.innerHTML = sourceHtml;
    fill.innerHTML = sourceHtml;

    element.innerHTML = "";
    element.setAttribute("aria-label", text);
    element.append(base, fill);
    element.dataset.layeredFillReady = "1";

    return fill;
  }

  prepareLayeredLineFill(element, lineClass, baseClass, fillClass) {
    if (!(element instanceof HTMLElement)) return [];
    if (element.dataset.layeredLineFillReady === "1") {
      return Array.from(element.querySelectorAll(`.${fillClass}`));
    }

    const sourceHtml = element.innerHTML.trim();
    const text = (element.textContent ?? "").replace(/\s+/g, " ").trim();
    const lines = sourceHtml.split(/<br\s*\/?>/i).map((l) => l.trim()).filter(Boolean);
    if (!lines.length) return [];

    const fragment = document.createDocumentFragment();
    lines.forEach((line) => {
      const lineWrap = document.createElement("span");
      const base = document.createElement("span");
      const fill = document.createElement("span");
      lineWrap.className = lineClass;
      base.className = baseClass;
      fill.className = fillClass;
      base.setAttribute("aria-hidden", "true");
      fill.setAttribute("aria-hidden", "true");
      base.innerHTML = line;
      fill.innerHTML = line;
      lineWrap.append(base, fill);
      fragment.appendChild(lineWrap);
    });

    element.innerHTML = "";
    element.setAttribute("aria-label", text);
    element.append(fragment);
    element.dataset.layeredLineFillReady = "1";

    return Array.from(element.querySelectorAll(`.${fillClass}`));
  }

  // ─── Private helpers ─────────────────────────────────

  #createTriggerOnEnter({ trigger, start, onEnter, once = true, invalidateOnRefresh = true }) {
    if (!(trigger instanceof HTMLElement) || typeof start !== "string" || typeof onEnter !== "function") return;

    let hasEntered = false;
    ScrollTrigger.create({
      trigger,
      start,
      once,
      invalidateOnRefresh,
      onEnter: () => {
        if (hasEntered) return;
        hasEntered = true;
        onEnter();
      },
    });
  }

  #collectRevealTargets(section, selectors) {
    return selectors.flatMap((selector) =>
      Array.from(section.querySelectorAll(selector)).filter((el) => el instanceof HTMLElement),
    );
  }

  // ─── Section-specific reveals ────────────────────────

  #initContactBannerParallax() {
    const sections = document.querySelectorAll(".no-contact-banner");
    if (!sections.length || window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    sections.forEach((section) => {
      if (!(section instanceof HTMLElement) || section.dataset.bannerParallaxBound === "1") return;

      const media = section.querySelector(".no-contact-banner__media");
      const mediaImage = media?.querySelector("img");
      if (!(media instanceof HTMLElement) || !(mediaImage instanceof HTMLImageElement)) return;

      section.dataset.bannerParallaxBound = "1";

      gsap.fromTo(
        mediaImage,
        { yPercent: -6, scale: 1.16 },
        {
          yPercent: 6,
          scale: 1.24,
          ease: "none",
          scrollTrigger: {
            trigger: section,
            start: "top bottom",
            end: "bottom top",
            scrub: 1,
            invalidateOnRefresh: true,
          },
        },
      );
    });
  }

  #initContactBannerReveal() {
    document.querySelectorAll(".no-contact-banner").forEach((section) => {
      if (!(section instanceof HTMLElement)) return;
      if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
        section.classList.add("is-revealed"); return;
      }
      if (section.dataset.contactBannerRevealBound === "1") return;

      const media = section.querySelector(".no-contact-banner__media");
      const contentTargets = this.#collectRevealTargets(section, [".no-contact-banner__content > *"]);
      if (!(media instanceof HTMLElement) || !contentTargets.length) {
        section.classList.add("is-revealed"); return;
      }

      section.dataset.contactBannerRevealBound = "1";

      this.#createTriggerOnEnter({
        trigger: section,
        start: RevealAnimator.#SUB_SECTION_REVEAL_START,
        onEnter: () => {
          gsap.timeline({ onComplete: () => section.classList.add("is-revealed") })
            .to(media, { autoAlpha: 1, scale: 1, duration: 0.92, ease: "power3.out" })
            .to(contentTargets, { autoAlpha: 1, y: 0, duration: 0.72, ease: "power3.out", stagger: 0.12 }, "-=0.56");
        },
      });
    });
  }

  #initFaqSectionReveal() {
    document.querySelectorAll(".no-sub-faq").forEach((section) => {
      if (!(section instanceof HTMLElement)) return;
      if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
        section.classList.add("is-revealed"); return;
      }
      if (section.dataset.faqRevealBound === "1") return;

      const background = section.querySelector(".no-sub-faq__bg");
      const title = section.querySelector(".no-sub-faq__txt");
      const body = section.querySelector(".no-sub-faq__body");
      const items = Array.from(section.querySelectorAll(".no-sub-faq__item"));
      const pagination = section.querySelector(".no-sub-faq__pagination");

      if (!(title instanceof HTMLElement) || !(body instanceof HTMLElement)) {
        section.classList.add("is-revealed"); return;
      }

      section.dataset.faqRevealBound = "1";

      this.#createTriggerOnEnter({
        trigger: section,
        start: RevealAnimator.#SUB_SECTION_REVEAL_START,
        onEnter: () => {
          const tl = gsap.timeline({
            defaults: { ease: "power3.out" },
            onComplete: () => section.classList.add("is-revealed"),
          });

          if (background instanceof HTMLElement) {
            tl.to(background, { autoAlpha: 1, scale: 1, duration: 1.02 }, 0);
          }

          tl.to([title, body], { autoAlpha: 1, y: 0, duration: 0.72, stagger: 0.12 }, 0.08)
            .to(items, { autoAlpha: 1, y: 0, duration: 0.58, stagger: 0.08 }, 0.22);

          if (pagination instanceof HTMLElement) {
            tl.to(pagination, { autoAlpha: 1, y: 0, duration: 0.56 }, 0.36);
          }
        },
      });
    });
  }

  #initApplyFormReveal() {
    document.querySelectorAll(".no-apply-form").forEach((section) => {
      if (!(section instanceof HTMLElement)) return;
      if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
        section.classList.add("is-revealed"); return;
      }
      if (section.dataset.applyFormRevealBound === "1") return;

      const background = section.querySelector(".no-apply-form__bg");
      const title = section.querySelector(".no-title-txt");
      const form = section.querySelector(".no-apply-form__form");
      const blocks = this.#collectRevealTargets(section, [
        ".no-apply-form__fields > *",
        ".no-apply-form__footer",
      ]);

      if (!(title instanceof HTMLElement) || !(form instanceof HTMLElement)) {
        section.classList.add("is-revealed"); return;
      }

      section.dataset.applyFormRevealBound = "1";

      this.#createTriggerOnEnter({
        trigger: section,
        start: RevealAnimator.#SUB_SECTION_REVEAL_START,
        onEnter: () => {
          const tl = gsap.timeline({
            defaults: { ease: "power3.out" },
            onComplete: () => section.classList.add("is-revealed"),
          });

          if (background instanceof HTMLElement) {
            tl.to(background, { autoAlpha: 1, scale: 1, duration: 1.02 }, 0);
          }

          tl.to([title, form], { autoAlpha: 1, y: 0, duration: 0.72, stagger: 0.12 }, 0.08)
            .to(blocks, { autoAlpha: 1, y: 0, duration: 0.6, stagger: 0.06 }, 0.24);
        },
      });
    });
  }
}
