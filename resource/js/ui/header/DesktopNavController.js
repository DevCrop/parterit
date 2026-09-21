export class DesktopNavController {
  #header;
  #desktopNav;
  #desktopGnb;
  #desktopGnbIndicator;
  #desktopSubmenus;
  #desktopNavMedia;
  #onExpand;
  #onCollapse;

  #submenuMasterTl = null;
  #submenuCloseTimer = 0;
  #indicatorSyncFrame = 0;

  constructor({ header, desktopNav, desktopGnb, desktopGnbIndicator, desktopSubmenus, desktopNavMedia, onExpand, onCollapse }) {
    this.#header = header;
    this.#desktopNav = desktopNav;
    this.#desktopGnb = desktopGnb;
    this.#desktopGnbIndicator = desktopGnbIndicator;
    this.#desktopSubmenus = desktopSubmenus;
    this.#desktopNavMedia = desktopNavMedia;
    this.#onExpand = onExpand;
    this.#onCollapse = onCollapse;
  }

  init() {
    if (!this.#desktopNav) return;

    this.#desktopNav.setAttribute("data-expanded", "false");
    this.#syncSubmenuHeights();
    this.#buildSubmenuTimeline();
    this.#syncIndicator();

    this.#desktopGnb?.querySelectorAll(".no-header__gnb-link").forEach((link) => {
      link.addEventListener("mouseenter", () => {
        if (!this.#desktopNavMedia.matches) return;
        // Per-link fallback so hover reliably opens the dropdown even when
        // the nav-level mouseenter is missed (trackpad coast-in, layout
        // shift on first paint, view-transition cross-document re-entry).
        this.expand();
        this.#moveIndicator(link);
      });
      link.addEventListener("focus", () => {
        if (!this.#desktopNavMedia.matches) return;
        this.#moveIndicator(link);
      });
    });

    this.#desktopGnb?.querySelectorAll(".no-header__gnb-item").forEach((item) => {
      // Hover anywhere within the menu item (incl. padding around the link)
      // also opens the dropdown. Redundant by design.
      item.addEventListener("mouseenter", () => {
        if (!this.#desktopNavMedia.matches) return;
        this.expand();
      });
    });

    this.#desktopGnb?.querySelectorAll(".no-header__lnb-link").forEach((link) => {
      const parentGnbLink = link.closest(".no-header__gnb-item")?.querySelector(".no-header__gnb-link");
      if (!(parentGnbLink instanceof HTMLElement)) return;
      link.addEventListener("mouseenter", () => {
        if (!this.#desktopNavMedia.matches) return;
        this.#moveIndicator(parentGnbLink);
      });
      link.addEventListener("focus", () => {
        if (!this.#desktopNavMedia.matches) return;
        this.#moveIndicator(parentGnbLink);
      });
    });

    this.#desktopNav.addEventListener("mouseenter", () => this.expand());
    this.#header.addEventListener("mouseleave", () => {
      this.collapse();
      this.#syncIndicator();
    });

    // macOS Safari fallback. Under `position: fixed` + `backdrop-filter` +
    // `will-change: transform`, `mouseenter` is unreliably hit-tested until
    // the user clicks (the "drag-to-open" symptom customers reported).
    // Pointer events route through a different pipeline and fire reliably.
    // `pointerover` bubbles, so a single delegated listener catches every
    // gnb-link / lnb-link the cursor crosses — no per-element wiring needed.
    this.#desktopNav.addEventListener("pointerover", (event) => {
      if (event.pointerType && event.pointerType !== "mouse") return;
      if (!this.#desktopNavMedia.matches) return;
      this.expand();
      const gnbLink = event.target.closest?.(".no-header__gnb-link");
      if (gnbLink instanceof HTMLElement) {
        this.#moveIndicator(gnbLink);
        return;
      }
      const lnbLink = event.target.closest?.(".no-header__lnb-link");
      if (lnbLink instanceof HTMLElement) {
        const parentGnbLink = lnbLink
          .closest(".no-header__gnb-item")
          ?.querySelector(".no-header__gnb-link");
        if (parentGnbLink instanceof HTMLElement) this.#moveIndicator(parentGnbLink);
      }
    });
    this.#header.addEventListener("pointerleave", (event) => {
      if (event.pointerType && event.pointerType !== "mouse") return;
      this.collapse();
      this.#syncIndicator();
    });

    this.#desktopNav.addEventListener("focusin", () => this.expand());
    this.#desktopNav.addEventListener("focusout", (event) => {
      if (event.relatedTarget instanceof Node && this.#header.contains(event.relatedTarget)) return;
      this.collapse();
      this.#syncIndicator();
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        this.collapse();
        this.#syncIndicator();
      }
    });

    if ("ResizeObserver" in window) {
      const ro = new ResizeObserver(() => {
        this.#syncSubmenuHeights();
        this.#buildSubmenuTimeline();
        this.#syncIndicator();
      });
      this.#desktopNav.querySelectorAll(".no-header__submenu").forEach((s) => ro.observe(s));
    }

    window.addEventListener("resize", () => {
      this.#syncSubmenuHeights();
      this.#buildSubmenuTimeline();
      this.#syncIndicator();
    });

    window.addEventListener("load", () => {
      this.#syncSubmenuHeights();
      this.#buildSubmenuTimeline();
      this.#syncIndicator();
    }, { once: true });

    this.#desktopNavMedia.addEventListener("change", (event) => {
      if (!event.matches) {
        this.reset();
        return;
      }
      this.#syncSubmenuHeights();
      this.#buildSubmenuTimeline();
      this.#syncIndicator();
    });
  }

  get isExpanded() {
    return Boolean(
      this.#desktopNav &&
      this.#desktopNavMedia.matches &&
      this.#desktopNav.getAttribute("data-expanded") === "true",
    );
  }

  expand() {
    if (!this.#desktopNav || !this.#desktopNavMedia.matches) return;
    // 서브메뉴가 없으면 hover 확장(백드롭·헤더 크롬)을 띄우지 않는다.
    if (!this.#desktopSubmenus.length) return;
    this.#syncSubmenuHeights();
    this.#desktopNav.setAttribute("data-expanded", "true");
    this.#header.classList.add("is-nav-expanded");
    this.#playSubmenuReveal();
    this.#onExpand?.();
  }

  collapse() {
    if (!this.#desktopNav || !this.#desktopNavMedia.matches) return;
    this.#desktopNav.setAttribute("data-expanded", "false");
    const submenuPromise = this.#reverseSubmenuReveal();
    this.#onCollapse?.(submenuPromise);
    submenuPromise.finally(() => this.#header.classList.remove("is-nav-expanded"));
    return submenuPromise;
  }

  reverseSubmenu() {
    return this.#reverseSubmenuReveal();
  }

  scheduleIndicatorSync() {
    if (!this.#desktopGnbIndicator) return;
    if (this.#indicatorSyncFrame) window.cancelAnimationFrame(this.#indicatorSyncFrame);
    this.#indicatorSyncFrame = window.requestAnimationFrame(() => {
      this.#indicatorSyncFrame = window.requestAnimationFrame(() => {
        this.#indicatorSyncFrame = 0;
        this.#syncIndicator();
      });
    });
  }

  reset() {
    this.#clearCloseTimer();
    this.#desktopNav?.classList.remove("is-submenu-active");
    this.#submenuMasterTl?.pause(0);
    this.#desktopNav?.setAttribute("data-expanded", "false");
    this.#header.classList.remove("is-nav-expanded");
    this.#desktopGnbIndicator?.classList.remove("is-visible");
  }

  // ─── Private ─────────────────────────────────────────

  #syncSubmenuHeights() {
    if (!this.#desktopNav) return;
    const submenus = this.#desktopNav.querySelectorAll(".no-header__submenu");
    let maxHeight = 0;
    submenus.forEach((submenu) => {
      const marginTop = parseFloat(window.getComputedStyle(submenu).marginTop) || 0;
      maxHeight = Math.max(maxHeight, submenu.scrollHeight + marginTop);
    });
    this.#header.style.setProperty("--header-nav-extra-height", `${maxHeight}px`);
  }

  #buildSubmenuTimeline() {
    if (!this.#desktopSubmenus.length) { this.#submenuMasterTl = null; return; }
    const items = this.#desktopSubmenus.flatMap((s) => Array.from(s.querySelectorAll(".no-header__lnb-item")));
    if (!items.length) { this.#submenuMasterTl = null; return; }
    this.#submenuMasterTl?.kill();
    this.#submenuMasterTl = gsap.timeline({ paused: true });
    this.#submenuMasterTl.to(items, {
      opacity: 1,
      y: 0,
      duration: 0.32,
      ease: "power3.out",
      stagger: { each: 0.045, from: "start" },
    });
  }

  #playSubmenuReveal() {
    if (!this.#submenuMasterTl) return;
    this.#clearCloseTimer();
    this.#desktopNav?.classList.add("is-submenu-active");
    this.#submenuMasterTl.play();
  }

  #reverseSubmenuReveal() {
    if (!this.#submenuMasterTl || !this.#desktopNav) {
      this.#desktopNav?.classList.remove("is-submenu-active");
      return Promise.resolve();
    }
    this.#clearCloseTimer();

    return new Promise((resolve) => {
      let settled = false;
      const prev = this.#submenuMasterTl.eventCallback("onReverseComplete");

      const finish = () => {
        if (settled) return;
        settled = true;
        window.clearTimeout(timerId);
        this.#desktopNav.classList.remove("is-submenu-active");
        this.#submenuCloseTimer = 0;
        this.#submenuMasterTl.eventCallback("onReverseComplete", prev ?? null);
        if (typeof prev === "function") prev();
        resolve();
      };

      const timerId = window.setTimeout(finish, 360);
      this.#submenuMasterTl.eventCallback("onReverseComplete", finish);
      if (this.#submenuMasterTl.progress() <= 0) { finish(); return; }
      this.#submenuMasterTl.reverse();
    });
  }

  #clearCloseTimer() {
    if (!this.#submenuCloseTimer) return;
    window.clearTimeout(this.#submenuCloseTimer);
    this.#submenuCloseTimer = 0;
  }

  #moveIndicator(link) {
    if (!this.#desktopNav || !this.#desktopGnbIndicator || !this.#desktopNavMedia.matches) return;
    if (!link) { this.#desktopGnbIndicator.classList.remove("is-visible"); return; }
    const label = link.querySelector(".no-header__gnb-label");
    const target = label instanceof HTMLElement ? label : link;
    const navRect = this.#desktopNav.getBoundingClientRect();
    const targetRect = target.getBoundingClientRect();
    this.#desktopGnbIndicator.style.transform = `translateX(${targetRect.left - navRect.left - 4}px)`;
    this.#desktopGnbIndicator.style.width = `${targetRect.width + 8}px`;
    this.#desktopGnbIndicator.classList.add("is-visible");
  }

  #syncIndicator() {
    if (!this.#desktopNavMedia.matches) {
      this.#desktopGnbIndicator?.classList.remove("is-visible");
      return;
    }
    const activeLink = this.#desktopGnb?.querySelector(".no-header__gnb-link.is-active") ?? null;
    this.#moveIndicator(activeLink);
  }
}
