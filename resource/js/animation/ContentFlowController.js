/** Marketing narrative: grouped copy first, supporting visual second.
 * Interactive rails, counters, hero and vision pin retain their own motion.
 */
export class ContentFlowController {
  init() {
    if (!document.querySelector('[class*="partnerit-"]') || !window.gsap || !window.ScrollTrigger) return;
    const root = document;
    const media = gsap.matchMedia();
    media.add({ all: '(min-width: 0px)', desktop: '(min-width: 1025px)', reduce: '(prefers-reduced-motion: reduce)' }, ({ conditions }) => {
      if (conditions.reduce) return;
      // Hero copy and CTAs stay immediately visible. Each group has its own
      // viewport trigger so stacked mobile content never waits off screen.
      const stages = [
        ['.partnerit-metrics h2, .partnerit-patent h2, .partnerit-problem h2', 'copy'],
        ['.partnerit-solutions__title, .partnerit-services__header, .partnerit-service-details__header, .partnerit-impact__header', 'copy'],
        ['.partnerit-solutions__grid, .partnerit-service-details__grid, .partnerit-impact__grid', 'visual'],
        ['.partnerit-services__visual', 'visual'],
        ['.partnerit-reviews__header, .partnerit-partners__header', 'copy'],
        ['.partnerit-pricing__header, .partnerit-pricing__addons-title', 'copy'],
        ['.partnerit-pricing__plans, .partnerit-pricing__addon-grid', 'copy'],
        ['.partnerit-final-cta__heading', 'copy'],
        ['.partnerit-company-flow h2, .partnerit-company-certificates__title, .partnerit-company-history__inner > h2', 'copy'],
        ['.partnerit-company-flow__grid', 'visual'],
        ['.partnerit-announcement__section .partnerit-announcement__copy, .partnerit-announcement-collection__heading, .partnerit-announcement-management__inner > h2, .partnerit-announcement-problem__inner > h2', 'copy'],
        ['.partnerit-announcement__section .partnerit-announcement__mockup, .partnerit-announcement-collection__grid, .partnerit-announcement-profile__list, .partnerit-announcement-management__grid', 'visual'],
        ['.partnerit-consortium-problem__header, .partnerit-consortium-matching__header, .partnerit-consortium-features h2, .partnerit-consortium-collaboration__header', 'copy'],
        ['.partnerit-consortium-problem__grid, .partnerit-consortium-matching__panel-motion, .partnerit-consortium-features__grid, .partnerit-consortium-collaboration__grid', 'visual'],
        ['.partnerit-stories-gallery__heading', 'copy'],
        ['.partnerit-stories-gallery__grid', 'visual'],
        ['.partnerit-ai-report-context__heading', 'copy'],
        ['.partnerit-ai-report-context__grid', 'visual'],
        ['.partnerit-ai-report-context__reading', 'copy'],
        ['.partnerit-ai-report-context__preview', 'visual'],
        ['.partnerit-ai-report-preparation__copy', 'copy'],
        ['.partnerit-ai-report-preparation .partnerit-ai-report__browser', 'visual'],
        ['.partnerit-ai-report-diagnosis__inner > h2', 'copy'],
        ['.partnerit-ai-report-diagnosis__visual', 'visual'],
        ['.partnerit-ai-report-diagnosis__grid', 'copy'],
        ['.partnerit-ai-report-capability__copy', 'copy'],
        ['.partnerit-ai-report-capability__visual', 'visual'],
      ];
      stages.forEach(([selector, role]) => {
        root.querySelectorAll(selector).forEach((target) => {
          if (!target || target.getBoundingClientRect().bottom <= 0) return;
          // NarrativeAos owns detailed sub-page items. Avoid stacking two
          // reveal systems on the same element or its animated children.
          if (target.matches('[data-aos]') || target.querySelector('[data-aos]')) return;

          const visual = role === 'visual';
          const y = conditions.desktop ? (visual ? 48 : 30) : (visual ? 30 : 18);
          gsap.fromTo(target, {
            opacity: 0,
            y,
          }, {
            opacity: 1, y: 0,
            duration: conditions.desktop ? (visual ? .9 : .68) : (visual ? .72 : .58),
            ease: 'power3.out', clearProps: 'opacity,transform',
            scrollTrigger: {
              trigger: target, start: visual ? 'top 84%' : 'top 88%',
              once: true, toggleActions: 'play none none none',
              invalidateOnRefresh: true,
            },
          });
        });
      });
    });
    document.fonts?.ready.then(() => ScrollTrigger.refresh());
    window.addEventListener('pagehide', (event) => {
      if (!event.persisted) media.revert();
    }, { once: true });
  }
}
