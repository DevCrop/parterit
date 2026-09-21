export class ImpactChartController {
  init() {
    const sections = document.querySelectorAll(".partnerit-impact");
    if (!sections.length || !window.gsap || !window.ScrollTrigger) return;
    if (window.matchMedia?.("(prefers-reduced-motion: reduce)").matches) return;

    sections.forEach((section) => {
      if (section.dataset.impactChartInitialized === "1") return;
      section.dataset.impactChartInitialized = "1";

      const bars = section.querySelectorAll(".partnerit-impact__bar");
      const donut = section.querySelector(".partnerit-impact__donut");
      const legends = section.querySelectorAll(".partnerit-impact__legend-item");
      const timeline = window.gsap.timeline({
        defaults: { ease: "power3.out" },
        scrollTrigger: {
          trigger: section.querySelector(".partnerit-impact__grid") || section,
          start: "top 78%",
          once: true,
        },
      });

      if (bars.length) {
        timeline.fromTo(
          bars,
          { scaleX: 0, transformOrigin: "left center" },
          { scaleX: 1, duration: 1.05, stagger: 0.12, clearProps: "transform,transformOrigin" },
        );
      }

      if (donut) {
        timeline.fromTo(
          donut,
          { "--impact-progress": 0, rotate: -18, opacity: 0.5 },
          { "--impact-progress": 83.3, rotate: 0, opacity: 1, duration: 1.1 },
          0.12,
        );
      }

      if (legends.length) {
        timeline.fromTo(
          legends,
          { opacity: 0, x: 10 },
          { opacity: 1, x: 0, duration: 0.45, stagger: 0.08, clearProps: "opacity,transform" },
          0.45,
        );
      }
    });
  }
}
