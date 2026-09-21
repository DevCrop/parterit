import Lenis from "lenis";

export class ScrollManager {
  #lenis = null;
  #locks = new Set();

  init() {
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    this.#lenis = new Lenis({
      duration: 1.05,
      lerp: 0.1,
      smoothWheel: true,
      wheelMultiplier: 0.95,
      touchMultiplier: 1,
      syncTouch: false,
      infinite: false,
    });

    this.#lenis.on("scroll", () => ScrollTrigger.update());

    gsap.ticker.add((time) => {
      this.#lenis.raf(time * 1000);
    });
    gsap.ticker.lagSmoothing(0);

    ScrollTrigger.addEventListener("refresh", () => this.#lenis.resize());

    window.addEventListener(
      "load",
      () => {
        this.#lenis.resize();
        ScrollTrigger.refresh();
      },
      { once: true },
    );

    this.#bindViewportEvents();

    window.__lenis = this.#lenis;
  }

  // iOS Safari fires `resize` before the post-rotation layout settles, so a single
  // listener captures stale viewport metrics. Debounce resize and re-measure twice
  // after orientationchange — once when the event lands, again after the toolbar
  // animation completes.
  #bindViewportEvents() {
    let resizeTimer = 0;
    const refresh = () => {
      this.#lenis?.resize();
      ScrollTrigger.refresh(true);
    };

    window.addEventListener("resize", () => {
      window.clearTimeout(resizeTimer);
      resizeTimer = window.setTimeout(refresh, 150);
    });

    window.addEventListener("orientationchange", () => {
      window.setTimeout(refresh, 200);
      window.setTimeout(refresh, 500);
    });
  }

  lock(key) {
    if (!key) return;
    this.#locks.add(key);
    this.#sync();
  }

  unlock(key) {
    if (!key) return;
    this.#locks.delete(key);
    this.#sync();
  }

  clearAll() {
    this.#locks.clear();
    this.#sync();
  }

  #sync() {
    const locked = this.#locks.size > 0;
    document.documentElement.classList.toggle("scroll-lock", locked);
    document.body.classList.toggle("scroll-lock", locked);
    document.body.classList.toggle("--hidden", locked);
    document.body.style.overflow = locked ? "hidden" : "";
    if (locked) {
      this.#lenis?.stop?.();
    } else {
      this.#lenis?.start?.();
    }
  }
}
