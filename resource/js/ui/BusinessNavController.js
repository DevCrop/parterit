/**
 * BusinessNavController
 * /business 한 페이지 섹션 NAV. 버튼 클릭 → 해당 섹션으로 스크롤 이동 + scroll-spy 활성표시.
 * Lenis 활성 시 부드러운 스크롤(window.__lenis.scrollTo).
 *   버튼 = .business-nav [data-target="<section id>"]
 */
export class BusinessNavController {
  #nav = null;
  #buttons = [];
  #sections = [];
  #observer = null;
  #activeId = null;

  init() {
    this.#nav = document.querySelector(".business-nav");
    if (!this.#nav) return;

    this.#buttons = [...this.#nav.querySelectorAll("[data-target]")];
    if (!this.#buttons.length) return;

    this.#sections = this.#buttons
      .map((btn) => document.getElementById(btn.dataset.target))
      .filter(Boolean);
    if (!this.#sections.length) return;

    this.#buttons.forEach((btn) =>
      btn.addEventListener("click", () => this.#scrollTo(btn.dataset.target)),
    );

    this.#setupObserver();
  }

  #scrollTo(id) {
    const el = document.getElementById(id);
    if (!el) return;

    const header = document.querySelector(".no-header");
    const offset =
      (header?.offsetHeight || 0) + (this.#nav?.offsetHeight || 0) + 8;

    const lenis = window.__lenis;
    if (lenis?.scrollTo) {
      lenis.scrollTo(el, { offset: -offset });
    } else {
      const top = el.getBoundingClientRect().top + window.scrollY - offset;
      window.scrollTo({ top, behavior: "smooth" });
    }
  }

  #setupObserver() {
    this.#observer = new IntersectionObserver(
      (entries) =>
        entries.forEach((e) => {
          if (e.isIntersecting) this.#setActive(e.target.id);
        }),
      { rootMargin: "-15% 0px -70% 0px", threshold: 0 },
    );
    this.#sections.forEach((s) => this.#observer.observe(s));
  }

  #setActive(id) {
    if (this.#activeId === id) return;
    this.#activeId = id;
    this.#buttons.forEach((btn) => {
      const isActive = btn.dataset.target === id;
      btn.classList.toggle("is-active", isActive);
      if (isActive) btn.setAttribute("aria-current", "true");
      else btn.removeAttribute("aria-current");
    });
  }
}
