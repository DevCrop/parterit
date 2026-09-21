export class PopupController {
  #swiper = null;

  init() {
    const root = document.querySelector("[data-popup-root]");
    if (!(root instanceof HTMLElement)) return;

    this.#removeSuppressedSlides(root);
    const slides = Array.from(root.querySelectorAll("[data-popup-slide]"));
    if (!slides.length) {
      root.remove();
      return;
    }

    this.#initSwiper(root, slides.length);
    this.#bindClose(root);
    this.#bindEscape(root);
  }

  #removeSuppressedSlides(root) {
    const today = this.#todayKey();
    root.querySelectorAll("[data-popup-slide]").forEach((slide) => {
      const id = slide.getAttribute("data-popup-id");
      if (!id) return;
      if (window.localStorage.getItem(this.#storageKey(id)) === today) {
        slide.remove();
      }
    });
  }

  #initSwiper(root, slideCount) {
    const swiperEl = root.querySelector("[data-popup-swiper]");
    if (!(swiperEl instanceof HTMLElement) || typeof window.Swiper !== "function") return;

    this.#swiper = new window.Swiper(swiperEl, {
      slidesPerView: 1,
      speed: 420,
      loop: slideCount > 1,
      watchOverflow: true,
      navigation: {
        prevEl: root.querySelector(".image-popup-prev"),
        nextEl: root.querySelector(".image-popup-next"),
      },
      pagination: {
        el: root.querySelector(".image-popup-pagination"),
        clickable: true,
      },
    });
  }

  #bindClose(root) {
    root.querySelectorAll("[data-popup-close]").forEach((button) => {
      button.addEventListener("click", () => this.#close(root));
    });
  }

  #bindEscape(root) {
    document.addEventListener("keydown", (event) => {
      if (!document.body.contains(root)) return;
      if (event.key === "Escape") this.#close(root);
    });
  }

  #close(root) {
    const todayCheckbox = root.querySelector("[data-popup-today]");
    if (todayCheckbox instanceof HTMLInputElement && todayCheckbox.checked) {
      const today = this.#todayKey();
      root.querySelectorAll("[data-popup-slide]").forEach((slide) => {
        const id = slide.getAttribute("data-popup-id");
        if (id) window.localStorage.setItem(this.#storageKey(id), today);
      });
    }

    if (this.#swiper) {
      this.#swiper.destroy(true, true);
      this.#swiper = null;
    }
    root.remove();
  }

  #storageKey(id) {
    const path = window.location.pathname.replace(/\/+$/, "") || "/";
    return `dkh-popup:${path}:${id}`;
  }

  #todayKey() {
    const now = new Date();
    const yyyy = now.getFullYear();
    const mm = String(now.getMonth() + 1).padStart(2, "0");
    const dd = String(now.getDate()).padStart(2, "0");
    return `${yyyy}-${mm}-${dd}`;
  }
}
