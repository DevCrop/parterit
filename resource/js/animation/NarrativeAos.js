export class NarrativeAos {
  constructor() {
    this.reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  }

  prepare() {
    if (this.reducedMotion) return;

    this.applyToAll(
      [
        ".partnerit-metrics h2",
        ".partnerit-patent h2",
        ".partnerit-problem h2",
        ".partnerit-solutions__title",
        ".partnerit-services__header",
        ".partnerit-service-details__header",
        ".partnerit-impact__header",
        ".partnerit-reviews__header",
        ".partnerit-partners__header",
        ".partnerit-pricing__header",
        ".partnerit-pricing__addons-title",
        ".partnerit-final-cta__heading",
        ".partnerit-company-flow h2",
        ".partnerit-patent__content",
        ".partnerit-services__copy",
        ".partnerit-announcement__copy",
        ".partnerit-announcement-collection__heading",
        ".partnerit-announcement-management h2",
        ".partnerit-ai-report-context__heading",
        ".partnerit-ai-report-preparation__copy",
        ".partnerit-ai-report-diagnosis h2",
        ".partnerit-ai-report-capability__copy",
        ".partnerit-consortium-problem__header",
        ".partnerit-consortium-matching__header",
        ".partnerit-consortium-features__title",
        ".partnerit-consortium-collaboration__header",
        ".partnerit-company-history__inner > h2",
        ".partnerit-company-certificates__inner > h2",
        ".partnerit-stories-gallery__heading",
        ".partnerit-story-detail__header",
        ".partnerit-legal__header",
      ],
      "partnerit-heading",
      { duration: 800, offset: 96 }
    );

    this.applyToAll(
      [
        ".partnerit-services__visual",
        ".partnerit-reviews__carousel",
        ".partnerit-partners__marquees",
        ".partnerit-company-certificates__swiper",
        ".partnerit-announcement__mockup",
        ".partnerit-announcement-profile__visual",
        ".partnerit-ai-report-context__preview",
        ".partnerit-ai-report-preparation .partnerit-ai-report__browser",
        ".partnerit-ai-report-diagnosis__visual",
        ".partnerit-ai-report-capability__visual",
        ".partnerit-consortium-matching__visual",
        ".partnerit-consortium-collaboration__visual",
        ".partnerit-stories-featured__swiper",
        ".partnerit-story-detail__visual",
        ".partnerit-story-detail__content",
        ".partnerit-final-cta__actions",
      ],
      "partnerit-visual",
      { duration: 1000, delay: 100, offset: 120 }
    );

    [
      [".partnerit-metrics__list", ".partnerit-metrics__card"],
      [".partnerit-problem__list", ".partnerit-problem__item"],
      [".partnerit-solutions__grid", ".partnerit-solutions__item"],
      [".partnerit-service-details__grid", ".partnerit-service-details__item"],
      [".partnerit-impact__grid", ".partnerit-impact__item"],
      [".partnerit-pricing__plans", ".partnerit-pricing__plan"],
      [".partnerit-pricing__addon-grid", ".partnerit-pricing__addon"],
      [".partnerit-company-flow__grid", ".partnerit-company-flow__card"],
      [".partnerit-company-history__years", ".partnerit-company-history__year"],
      [".partnerit-announcement-problem__conditions", ".partnerit-announcement-problem__condition"],
      [".partnerit-announcement-profile__list", ".partnerit-announcement-profile__item"],
      [".partnerit-announcement-selection__conditions", "li"],
      [".partnerit-announcement-collection__grid", ".partnerit-announcement-collection__card"],
      [".partnerit-announcement-management__grid", ".partnerit-announcement-management__card"],
      [".partnerit-ai-report-context__grid", ".partnerit-ai-report-context__card"],
      [".partnerit-ai-report-preparation__list", ".partnerit-ai-report-preparation__item"],
      [".partnerit-ai-report-diagnosis__grid", ".partnerit-ai-report-diagnosis__card"],
      [".partnerit-ai-report-capability__cards", ".partnerit-ai-report-capability__card"],
      [".partnerit-consortium-problem__grid", ".partnerit-consortium-problem__card"],
      [".partnerit-consortium-features__grid", ".partnerit-consortium-features__card"],
      [".partnerit-consortium-collaboration__grid", ".partnerit-consortium-collaboration__card"],
      [".partnerit-stories-gallery__grid", ".partnerit-story-card"],
      [".partnerit-legal__content", ".partnerit-legal__article"],
    ].forEach(([group, item]) => this.applyStagger(group, item));
  }

  applyToAll(selectors, animation, options = {}) {
    document.querySelectorAll(selectors.join(",")).forEach((element) => {
      this.configure(element, animation, options);
    });
  }

  applyStagger(groupSelector, itemSelector) {
    document.querySelectorAll(groupSelector).forEach((group) => {
      group.querySelectorAll(itemSelector).forEach((item, index) => {
        this.configure(item, "partnerit-card", {
          duration: 700,
          delay: Math.min(index * 100, 300),
          offset: 64,
        });
      });
    });
  }

  configure(element, animation, options) {
    if (element.hasAttribute("data-aos")) return;

    element.dataset.aos = animation;
    Object.entries(options).forEach(([key, value]) => {
      element.dataset[`aos${key.charAt(0).toUpperCase()}${key.slice(1)}`] = String(value);
    });
  }
}
