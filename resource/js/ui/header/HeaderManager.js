import { DesktopNavController } from "./DesktopNavController.js";

const HEADER_BACKGROUND_THRESHOLD = 80;

export class HeaderManager {
  #scrollManager;
  #header = null;
  #drawer = null;
  #backdrop = null;
  #modalCloseBtn = null;
  #drawerOpenBtn = null;
  #drawerCenter = null;
  #desktopNav = null;
  #desktopNavMedia = null;
  #desktopNavController = null;

  #drawerTl = null;
  #focusTrapHandler = null;
  #drawerMenuTextTween = null;
  #headerChromeTween = null;

  #headerBaseHeight = 0;
  #isColorMode = false;
  #navigationInFlight = false;

  constructor(scrollManager) {
    this.#scrollManager = scrollManager;
  }

  init() {
    this.#header = document.getElementById("main-header");
    if (!this.#header) return;

    this.#drawer = document.getElementById("main-drawer");
    this.#backdrop = document.getElementById("backdrop");
    this.#modalCloseBtn = document.querySelector(".no-modal__close");
    this.#drawerOpenBtn = this.#header.querySelector(".no-header__toggle");
    this.#drawerCenter = this.#drawer?.querySelector(".no-drawer__center") ?? null;
    this.#desktopNav = this.#header.querySelector(".no-header__nav");
    this.#desktopNavMedia = window.matchMedia("(min-width: 1025px)");

    this.#desktopNavController = new DesktopNavController({
      header: this.#header,
      desktopNav: this.#desktopNav,
      desktopGnb: this.#header.querySelector(".no-header__gnb"),
      desktopGnbIndicator: this.#header.querySelector(".no-header__gnb-indicator"),
      desktopSubmenus: Array.from(this.#header.querySelectorAll(".no-header__submenu")),
      desktopNavMedia: this.#desktopNavMedia,
      onExpand: () => {
        this.#openBackdrop();
        this.#applyHeaderChrome({ expanded: true });
      },
      onCollapse: (submenuPromise) => {
        this.#closeBackdrop();
        submenuPromise.then(() =>
          this.#applyHeaderChrome({ expanded: false, duration: 0.3, ease: "power2.out" }),
        );
      },
    });
    this.#desktopNavController.init();

    this.#buildDrawerTimeline();
    this.#bindDrawerEvents();
    this.#bindBackdropEvents();
    this.#bindKeyboardEvents();
    this.#bindHeroObserver();
    this.#bindCareerHeroObserver();
    this.#bindScrollHandler();
    this.#bindLightSectionObserver();
    this.#bindNavigationIntercept();
    this.#bindViewTransitionHooks();
    this.#bindWindowEvents();

    this.#isColorMode = this.#header.classList.contains("--color");
    this.#syncBaseHeight();
    this.#syncScrollState({ immediate: true });
    this.#applyHeaderChrome({ immediate: true });
  }

  // ─── Backdrop ────────────────────────────────────────
  //
  // CSS-driven: the `.is-active` class controls opacity, visibility, and
  // pointer-events together (see `_backdrop.scss`). No GSAP, no inline
  // opacity — so partial-open / interrupted states cannot persist as
  // "invisible but click-blocking" leftovers across navigations.

  #openBackdrop() {
    if (!this.#backdrop) return;
    this.#backdrop.setAttribute("aria-hidden", "false");
    this.#backdrop.classList.add("is-active");
  }

  #closeBackdrop(force = false) {
    if (!this.#backdrop) return;
    if (force) {
      this.#backdrop.classList.remove("is-active");
      this.#backdrop.setAttribute("aria-hidden", "true");
      return;
    }
    const drawerOpen = this.#drawer?.getAttribute("data-state") === "open";
    if (!drawerOpen && !this.#desktopNavController?.isExpanded) {
      this.#backdrop.classList.remove("is-active");
      this.#backdrop.setAttribute("aria-hidden", "true");
    }
  }

  // ─── Drawer ──────────────────────────────────────────

  #buildDrawerTimeline() {
    if (!this.#drawer) return;
    this.#drawerTl = gsap.timeline({ paused: true });
    this.#drawerTl.to(this.#drawer, { x: "0%", opacity: 1, ease: "power3.out", duration: 0.24 });
    this.#drawerTl.eventCallback("onComplete", () => {
      if (this.#drawer.getAttribute("data-state") !== "open") return;
      this.#getDrawerFocusableElements()[0]?.focus({ preventScroll: true });
    });
    this.#drawerTl.eventCallback("onReverseComplete", () => {
      this.#drawer.classList.remove("is-active");
    });
  }

  #openDrawer() {
    if (!this.#drawer || !this.#drawerTl) return;
    this.#scrollManager.lock("drawer");
    this.#drawer.setAttribute("data-state", "open");
    this.#drawer.setAttribute("aria-hidden", "false");
    this.#drawer.classList.add("is-active");
    this.#header.classList.add("is-drawer-open");
    this.#header.classList.remove("is-hidden");
    this.#setToggleIcon(true);
    if (this.#drawerCenter) this.#drawerCenter.scrollTop = 0;
    this.#drawerTl.play();
    this.#openBackdrop();
    this.#drawerOpenBtn?.setAttribute("aria-expanded", "true");
    this.#drawerOpenBtn?.setAttribute("aria-label", "사이트 메뉴 닫기");

    this.#focusTrapHandler = (e) => {
      if (e.key !== "Tab") return;
      const focusable = this.#getDrawerFocusableElements();
      const first = focusable[0];
      const last = focusable[focusable.length - 1];
      if (!first) return;
      if (e.shiftKey) {
        if (document.activeElement === first) { e.preventDefault(); last?.focus({ preventScroll: true }); }
      } else {
        if (document.activeElement === last) { e.preventDefault(); first.focus({ preventScroll: true }); }
      }
    };
    this.#drawer.addEventListener("keydown", this.#focusTrapHandler);

    if (window.innerWidth <= 1024) {
      const targets = this.#drawer.querySelectorAll(
        ".no-drawer__gnb-item, .no-drawer__lnb-item, .no-drawer__cta-wrap",
      );
      this.#drawerMenuTextTween?.kill();
      gsap.set(targets, { opacity: 0, y: 24 });
      this.#drawerMenuTextTween = gsap.to(targets, {
        opacity: 1,
        y: 0,
        duration: 0.52,
        ease: "power3.out",
        stagger: 0.055,
        delay: 0.08,
        overwrite: true,
      });
    }
  }

  #getDrawerFocusableElements() {
    if (!this.#drawer) return [];
    return Array.from(this.#drawer.querySelectorAll(
      'a[href], button:not([disabled]), [tabindex]'
    )).filter((element) => {
      if (element.tabIndex < 0 || element.matches(":disabled") || element.closest("[inert]")) return false;
      const style = window.getComputedStyle(element);
      return element.getClientRects().length > 0 && style.display !== "none" && style.visibility === "visible";
    });
  }

  #closeDrawer({ wait = false } = {}) {
    if (!this.#drawer || !this.#drawerTl) return;
    const waitPromise = wait ? this.#waitForTimelineReverse(this.#drawerTl) : null;
    this.#drawerTl.reverse();
    this.#drawer.setAttribute("data-state", "closed");
    this.#drawer.setAttribute("aria-hidden", "true");
    this.#drawerOpenBtn?.setAttribute("aria-expanded", "false");
    this.#drawerOpenBtn?.setAttribute("aria-label", "사이트 메뉴 열기");
    this.#header.classList.remove("is-drawer-open");
    this.#setToggleIcon(false);
    this.#scrollManager.unlock("drawer");
    this.#closeBackdrop();
    this.#modalCloseBtn?.classList.remove("is-visible");
    this.#drawerMenuTextTween?.kill();
    if (this.#focusTrapHandler) {
      this.#drawer.removeEventListener("keydown", this.#focusTrapHandler);
      this.#focusTrapHandler = null;
    }
    requestAnimationFrame(() => this.#drawerOpenBtn?.focus());
    return waitPromise;
  }

  // ─── Header Chrome ────────────────────────────────────

  #syncBaseHeight() {
    const inner = this.#header.querySelector(".no-header__inner");
    this.#headerBaseHeight =
      inner?.getBoundingClientRect().height ||
      this.#header.getBoundingClientRect().height ||
      this.#headerBaseHeight ||
      0;
  }

  #getNavExtraHeight() {
    return parseFloat(
      window.getComputedStyle(this.#header).getPropertyValue("--header-nav-extra-height"),
    ) || 0;
  }

  #getChromeTarget(expanded = this.#desktopNavController?.isExpanded) {
    const base = this.#headerBaseHeight;
    const height = expanded ? base + this.#getNavExtraHeight() : base;
    return {
      height,
      surfaceOpacity: 0.06,
      surfaceBlur: "24px",
      shadowOpacity: 0.08,
    };
  }

  #applyHeaderChrome({ expanded, immediate = false, delay = 0, duration, ease } = {}) {
    const target = this.#getChromeTarget(expanded);
    this.#headerChromeTween?.kill();

    if (immediate) {
      gsap.set(this.#header, {
        height: target.height,
        "--header-surface-opacity": target.surfaceOpacity,
        "--header-surface-blur": target.surfaceBlur,
        "--header-shadow-opacity": target.shadowOpacity,
      });
      return null;
    }

    return new Promise((resolve) => {
      this.#headerChromeTween = gsap.to(this.#header, {
        height: target.height,
        "--header-surface-opacity": target.surfaceOpacity,
        "--header-surface-blur": target.surfaceBlur,
        "--header-shadow-opacity": target.shadowOpacity,
        delay,
        duration: duration ?? (expanded ? 0.38 : 0.28),
        ease: ease ?? (expanded ? "power3.out" : "power2.out"),
        overwrite: true,
        onComplete: () => { this.#headerChromeTween = null; resolve(); },
      });
    });
  }

  // ─── Scroll Behavior ─────────────────────────────────

  #syncScrollState({ immediate = false } = {}) {
    const shouldBeScrolled = window.scrollY > HEADER_BACKGROUND_THRESHOLD;
    const wasScrolled = this.#header.classList.contains("is-scrolled");
    this.#header.classList.toggle("is-scrolled", shouldBeScrolled);
    if (shouldBeScrolled === wasScrolled || this.#desktopNavController?.isExpanded) return;
    this.#applyHeaderChrome({ immediate });
  }

  #bindScrollHandler() {
    // Header hide-on-scroll removed by request: the header no longer slides out
    // on scroll-down / back in on scroll-up. Only the scrolled-background state
    // (`is-scrolled`) still tracks scroll position.
    window.addEventListener("scroll", () => {
      this.#syncScrollState();
    }, { passive: true });
  }

  // ─── Light Section Observer ───────────────────────────

  #bindLightSectionObserver() {
    const lightSections = Array.from(
      document.querySelectorAll(".no-main-media, .no-sub-media, .no-sub-cases"),
    );
    if (!lightSections.length) return;

    const intersecting = new Set();
    let observer = null;

    const create = () => {
      observer?.disconnect();
      intersecting.clear();
      const marginBottom = Math.max(0, window.innerHeight - this.#header.offsetHeight);
      observer = new IntersectionObserver(
        (entries) => {
          entries.forEach((e) => e.isIntersecting ? intersecting.add(e.target) : intersecting.delete(e.target));
          this.#header.classList.toggle("is-on-light-section", intersecting.size > 0);
        },
        { rootMargin: `0px 0px -${marginBottom}px 0px`, threshold: 0 },
      );
      lightSections.forEach((s) => observer.observe(s));
    };

    create();
    let resizeTimer = 0;
    window.addEventListener("resize", () => {
      window.clearTimeout(resizeTimer);
      resizeTimer = window.setTimeout(create, 200);
    });
    window.addEventListener("orientationchange", () => {
      window.setTimeout(create, 500);
    });
  }

  // ─── Hero Observer ────────────────────────────────────

  #bindCareerHeroObserver() {
    const hero = document.querySelector(".no-career-hero");
    if (!hero) return;

    // Hero is dark — strip --color immediately so #isColorMode initialises correctly below.
    this.#header.classList.remove("--color");

    new IntersectionObserver(
      (entries) => {
        const isVisible = entries[0].isIntersecting;
        this.#header.classList.toggle("--color", !isVisible);
        this.#isColorMode = !isVisible;
        this.#applyHeaderChrome();
      },
      { threshold: 0 },
    ).observe(hero);
  }

  #bindHeroObserver() {
    const hero = document.querySelector(".no-main-hero");
    if (!hero) return;
    new IntersectionObserver(
      (entries) => entries.forEach((e) => this.#header.classList.toggle("visual", e.isIntersecting)),
      { threshold: 0.1 },
    ).observe(hero);
  }

  // ─── Event Bindings ───────────────────────────────────

  #bindDrawerEvents() {
    if (!this.#drawer || !this.#drawerTl) return;
    this.#drawerOpenBtn?.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      if (this.#drawer?.getAttribute("data-state") === "open") {
        this.#closeDrawer();
      } else {
        this.#openDrawer();
      }
    });
  }

  #setToggleIcon(open) {
    const icon = this.#drawerOpenBtn?.querySelector("i");
    if (!icon) return;
    icon.classList.toggle("fa-bars", !open);
    icon.classList.toggle("fa-xmark", open);
  }

  #bindBackdropEvents() {
    if (!this.#backdrop) return;
    this.#backdrop.addEventListener("click", (e) => {
      if (e.target !== this.#backdrop) return;
      if (this.#drawer?.getAttribute("data-state") === "open") this.#closeDrawer();
    });
    this.#modalCloseBtn?.addEventListener("click", () => {
      if (this.#drawer?.getAttribute("data-state") === "open") this.#closeDrawer();
    });
  }

  #bindKeyboardEvents() {
    document.addEventListener("keydown", (e) => {
      if (e.key !== "Escape") return;
      if (this.#drawer?.getAttribute("data-state") === "open") this.#closeDrawer();
    });
  }

  #bindWindowEvents() {
    const resync = () => {
      this.#syncBaseHeight();
      this.#applyHeaderChrome({ immediate: true });
    };

    let resizeTimer = 0;
    window.addEventListener("resize", () => {
      window.clearTimeout(resizeTimer);
      resizeTimer = window.setTimeout(resync, 100);
    });

    // iOS Safari: `resize` after rotation lands before the URL bar settles, so
    // re-measure once when it fires and again after the toolbar animation ends.
    window.addEventListener("orientationchange", () => {
      window.setTimeout(resync, 200);
      window.setTimeout(resync, 500);
    });

    window.addEventListener("load", resync, { once: true });
  }

  // ─── Navigation Intercept ─────────────────────────────

  #bindNavigationIntercept() {
    document.addEventListener("click", async (event) => {
      const link = event.target.closest("a");
      if (!this.#isInterceptable(link, event)) return;

      const hasEphemeralUi =
        this.#desktopNavController?.isExpanded ||
        this.#drawer?.getAttribute("data-state") === "open" ||
        this.#header.classList.contains("is-hidden");

      if (!hasEphemeralUi || this.#navigationInFlight) return;

      event.preventDefault();
      this.#navigationInFlight = true;

      try {
        await this.#closeAllBeforeNavigation();
        window.location.assign(link.href);
      } finally {
        window.setTimeout(() => { this.#navigationInFlight = false; }, 400);
      }
    });
  }

  async #closeAllBeforeNavigation() {
    const tasks = [];

    if (this.#desktopNavController?.isExpanded) {
      // Wait for submenu only; chrome animation is non-blocking to prevent hang on kill.
      const submenuDone = this.#desktopNavController.reverseSubmenu();
      this.#closeBackdrop();
      this.#desktopNav?.setAttribute("data-expanded", "false");
      submenuDone
        .then(() => this.#applyHeaderChrome({ expanded: false, duration: 0.3, ease: "power2.out" }))
        .finally(() => this.#header.classList.remove("is-nav-expanded"));
      tasks.push(submenuDone);
    }

    if (this.#drawer?.getAttribute("data-state") === "open") {
      const p = this.#closeDrawer({ wait: true });
      if (p) tasks.push(p);
    }

    if (this.#header.classList.contains("is-hidden")) {
      this.#header.classList.remove("is-hidden");
      tasks.push(this.#waitForHeaderTransition("transform"));
    }

    if (!tasks.length) return false;

    const timeout = new Promise((resolve) => window.setTimeout(resolve, 500));
    await Promise.race([Promise.all(tasks), timeout]);
    return true;
  }

  #isInterceptable(link, event) {
    if (!(link instanceof HTMLAnchorElement) || !link.href) return false;
    if (event.defaultPrevented || event.button !== 0) return false;
    if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return false;
    if (link.hasAttribute("download")) return false;
    if ((link.getAttribute("target") || "").toLowerCase() === "_blank") return false;
    const url = new URL(link.href, window.location.href);
    if (url.origin !== window.location.origin) return false;
    if (url.pathname === window.location.pathname && url.hash) return false;
    return true;
  }

  // ─── View Transition Hooks ────────────────────────────

  #bindViewTransitionHooks() {
    window.addEventListener("pageswap", () => this.#freezeForSnapshot());
    window.addEventListener("pagereveal", () => this.#restoreAfterSnapshot());
    window.addEventListener("pageshow", () => this.#restoreAfterSnapshot());
  }

  #freezeForSnapshot() {
    // Backdrop is now CSS-only — clear the live state so the snapshot does
    // not freeze a half-open backdrop that would persist into the next page.
    if (this.#backdrop) {
      this.#backdrop.classList.remove("is-active");
      this.#backdrop.setAttribute("aria-hidden", "true");
    }
    this.#drawerTl?.kill();
    this.#drawerTl = null;
    this.#headerChromeTween?.kill();
    this.#header.style.transition = "none";
    this.#header.style.height = `${this.#header.offsetHeight}px`;
    this.#header.classList.add("is-transition-freeze");
    void this.#header.offsetHeight;
  }

  #restoreAfterSnapshot() {
    this.#normalizeEphemeralUi();
    this.#buildDrawerTimeline();
    this.#isColorMode = this.#header.classList.contains("--color");
    this.#header.style.transition = "";
    this.#header.style.height = "";
    this.#header.classList.remove("is-transition-freeze");
    this.#syncScrollState();
    this.#applyHeaderChrome({ immediate: true });
    this.#desktopNavController?.scheduleIndicatorSync();
    document.fonts?.ready.then(() => this.#desktopNavController?.scheduleIndicatorSync());
  }

  #normalizeEphemeralUi() {
    this.#desktopNavController?.reset();
    this.#header.classList.remove("is-nav-expanded", "is-hidden", "is-drawer-open");
    this.#setToggleIcon(false);
    this.#drawerOpenBtn?.setAttribute("aria-label", "사이트 메뉴 열기");

    if (this.#backdrop) {
      this.#backdrop.classList.remove("is-active");
      this.#backdrop.setAttribute("aria-hidden", "true");
    }
    if (this.#drawer) {
      this.#drawerTl?.pause(0);
      this.#drawer.classList.remove("is-active");
      this.#drawer.setAttribute("data-state", "closed");
      this.#drawer.setAttribute("aria-hidden", "true");
      this.#drawerOpenBtn?.setAttribute("aria-expanded", "false");
      gsap.set(this.#drawer, { clearProps: "x,opacity" });
    }

    ["drawer", "apply-result-modal"].forEach((k) => this.#scrollManager.unlock(k));
    this.#modalCloseBtn?.classList.remove("is-visible");
    this.#applyHeaderChrome({ immediate: true });
  }

  // ─── Helpers ─────────────────────────────────────────

  #waitForHeaderTransition(propertyName, timeout = 320) {
    return new Promise((resolve) => {
      let settled = false;
      const finish = () => {
        if (settled) return;
        settled = true;
        this.#header.removeEventListener("transitionend", onEnd);
        window.clearTimeout(timerId);
        resolve();
      };
      const onEnd = (e) => {
        if (e.target !== this.#header) return;
        if (propertyName && e.propertyName !== propertyName) return;
        finish();
      };
      const timerId = window.setTimeout(finish, timeout);
      this.#header.addEventListener("transitionend", onEnd);
    });
  }

  #waitForTimelineReverse(timeline, timeout = 320) {
    return new Promise((resolve) => {
      if (!timeline) { resolve(); return; }
      let settled = false;
      const prev = timeline.eventCallback("onReverseComplete");
      const finish = () => {
        if (settled) return;
        settled = true;
        window.clearTimeout(timerId);
        timeline.eventCallback("onReverseComplete", prev ?? null);
        if (typeof prev === "function") prev();
        resolve();
      };
      const timerId = window.setTimeout(finish, timeout);
      timeline.eventCallback("onReverseComplete", finish);
      timeline.reverse();
    });
  }
}
