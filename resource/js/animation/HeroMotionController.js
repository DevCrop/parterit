export class HeroMotionController {
  init() {
    const gsap = window.gsap;
    if (!gsap || window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    this.animateCompany(gsap);

    [
      {
        root: ".partnerit-announcement-hero",
        copy: [
          ".partnerit-announcement-hero__eyebrow",
          ".partnerit-announcement-hero__title span",
          ".partnerit-announcement-hero__description span",
        ],
        visual: ".partnerit-announcement-hero__dashboard",
      },
      {
        root: ".partnerit-ai-report-hero",
        copy: [
          ".partnerit-ai-report__eyebrow",
          "#ai-report-hero-title",
          ".partnerit-ai-report__description",
          ".partnerit-cta-link",
        ],
        visual: ".partnerit-ai-report__browser",
      },
      {
        root: ".partnerit-consortium-hero",
        copy: [
          ".partnerit-consortium-hero__title span",
          ".partnerit-consortium-hero__description span",
        ],
        visual: ".partnerit-consortium-hero__dashboard",
      },
      {
        root: ".partnerit-subhero",
        copy: [
          ".partnerit-subhero__eyebrow",
          ".partnerit-subhero__title span",
          ".partnerit-subhero__description span",
        ],
      },
    ].forEach((config) => this.animateHero(gsap, config));
  }

  animateCompany(gsap) {
    const root = document.querySelector(".partnerit-company-hero");
    if (!root) return;

    const kicker = root.querySelector(".partnerit-company-hero__kicker");
    const headline = root.querySelector(".partnerit-company-hero__headline");
    const description = root.querySelectorAll(".partnerit-company-hero__description span");
    if (!kicker || !headline || !description.length) return;

    gsap.timeline({ defaults: { ease: "power3.out" } })
      .fromTo(kicker, { autoAlpha: 0, y: 24 }, { autoAlpha: 1, y: 0, duration: 0.72 }, 0.08)
      .fromTo(headline, { autoAlpha: 0, y: 36, scale: 0.96 }, {
        autoAlpha: 1,
        y: 0,
        scale: 1,
        duration: 0.92,
        clearProps: "transform,opacity,visibility",
      }, 0.18)
      .fromTo(description, { autoAlpha: 0, y: 18 }, {
        autoAlpha: 1,
        y: 0,
        duration: 0.62,
        stagger: 0.1,
        clearProps: "transform,opacity,visibility",
      }, 0.48);
  }

  animateHero(gsap, { root: rootSelector, copy, visual }) {
    const root = document.querySelector(rootSelector);
    if (!root) return;

    const copyTargets = copy.flatMap((selector) => [...root.querySelectorAll(selector)]);
    if (!copyTargets.length) return;

    const timeline = gsap.timeline({ defaults: { ease: "power3.out" } });
    timeline.fromTo(copyTargets, { autoAlpha: 0, y: 28 }, {
      autoAlpha: 1,
      y: 0,
      duration: 0.72,
      stagger: 0.09,
      clearProps: "transform,opacity,visibility",
    }, 0.08);

    const visualTarget = visual ? root.querySelector(visual) : null;
    if (visualTarget) {
      timeline.fromTo(visualTarget, { autoAlpha: 0, y: 56 }, {
        autoAlpha: 1,
        y: 0,
        duration: 1,
        clearProps: "transform,opacity,visibility",
      }, 0.32);
    }
  }
}
