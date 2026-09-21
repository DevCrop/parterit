/** Progressive enhancement: without motion support all copy remains readable. */
export class CompanyVisionController {
  init() {
    const section = document.querySelector('.partnerit-company-vision');
    if (!section || !window.gsap || !window.ScrollTrigger) return;
    const background = section.querySelector('.partnerit-company-vision__background');
    const header = section.querySelector('.partnerit-company-vision__header');
    const message = section.querySelector('.partnerit-company-vision__message');
    const photos = section.querySelectorAll('.partnerit-company-vision__photo');
    const media = gsap.matchMedia();
    media.add({
      desktop: '(min-width: 768px) and (min-height: 700px)',
      reduce: '(prefers-reduced-motion: reduce)',
    }, (context) => {
      if (context.conditions.reduce || !context.conditions.desktop) return;
      // Enlarged text must remain readable, without a viewport crop.
      if (section.offsetHeight > window.innerHeight + 2) return;
      const timeline = gsap.timeline({
        scrollTrigger: {
          id: 'company-vision', trigger: section, start: 'top top',
          end: () => '+=' + Math.round(window.innerHeight * 2.5),
          pin: true, scrub: 0.65, anticipatePin: 1, invalidateOnRefresh: true,
        },
      });
      timeline.fromTo(background, { opacity: 0 }, { opacity: 1, duration: 1.2, ease: 'none' }, 0)
        .fromTo(photos, { opacity: 0, y: () => window.innerHeight * .85 }, { opacity: 1, y: 0, duration: 1.2, stagger: .45, ease: 'power2.out' }, .2)
        .fromTo(header, { opacity: 0, y: 28 }, { opacity: 1, y: 0, duration: .8 }, 1.25)
        .fromTo(message, { opacity: 0, y: 24 }, {
          opacity: 1, y: 0, duration: 1.1, ease: 'power2.out',
        }, 1.75)
        .to({}, { duration: 0.8 });
    });
    document.fonts?.ready.then(() => ScrollTrigger.refresh());
    window.addEventListener('pagehide', (event) => {
      if (!event.persisted) media.revert();
    }, { once: true });
  }
}
