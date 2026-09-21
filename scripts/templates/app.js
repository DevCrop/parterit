import Lenis from "lenis";
import { ThemeManager } from "./modules/theme/ThemeManager.js";
import { viewTransitionManager } from "./modules/view-transitions/ViewTransitionManager.js";
import { initSubFaqAccordion } from "./component/faq/SubFaqAccordion.js";

document.documentElement.classList.add("js");

$(document).ready(function () {
  viewTransitionManager.init();
  gsap.registerPlugin(ScrollTrigger);
  initLenis();
  initCharHoverBtns();
  initHeaderGnbCharHover();
  const themeManager = new ThemeManager();

  if (window.AOS && typeof window.AOS.init === "function") {
    window.AOS.init({
      duration: 520,
      easing: "ease-out-quart",
      once: true,
      offset: 0,
    });
  }

  initHeader();
  initHeaderLightSectionObserver();
  initFloatingButton();
  initMarquee();
  initContactBannerParallax();
  initContactBannerReveal();
  initFaqSectionReveal();
  initFooterReveal();
  initApplyFormInteraction();
  initSubMediaSwiper();
  initCategorySwiper();
  initContactLocationTabs();
  initSubFaqAccordion();

  setTimeout(() => {
    const theme = themeManager.getCurrentTheme();
    themeManager.updateLordIcons(theme);
  }, 1000);
});

function initLenis() {
  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
    return null;
  }

  const lenis = new Lenis({
    duration: 1.05,
    lerp: 0.1,
    smoothWheel: true,
    wheelMultiplier: 0.95,
    touchMultiplier: 1,
    syncTouch: false,
    infinite: false,
  });

  lenis.on("scroll", () => {
    ScrollTrigger.update();
  });

  gsap.ticker.add((time) => {
    lenis.raf(time * 1000);
  });
  gsap.ticker.lagSmoothing(0);

  ScrollTrigger.addEventListener("refresh", () => {
    lenis.resize();
  });

  window.addEventListener(
    "load",
    () => {
      lenis.resize();
      ScrollTrigger.refresh();
    },
    { once: true },
  );

  window.__lenis = lenis;

  return lenis;
}

const globalScrollLocks = new Set();

function syncGlobalScrollLockState() {
  const shouldLock = globalScrollLocks.size > 0;

  document.documentElement.classList.toggle("scroll-lock", shouldLock);
  document.body.classList.toggle("scroll-lock", shouldLock);
  document.body.classList.toggle("--hidden", shouldLock);
  document.body.style.overflow = shouldLock ? "hidden" : "";

  if (shouldLock) {
    window.__lenis?.stop?.();
    return;
  }

  window.__lenis?.start?.();
}

function lockGlobalScroll(key) {
  if (!key) return;
  globalScrollLocks.add(key);
  syncGlobalScrollLockState();
}

function unlockGlobalScroll(key) {
  if (!key) return;
  globalScrollLocks.delete(key);
  syncGlobalScrollLockState();
}

function clearGlobalScrollLocks() {
  globalScrollLocks.clear();
  syncGlobalScrollLockState();
}

function initHeader() {
  const header = document.getElementById("main-header");

  if (!header) return;

  const drawer = document.getElementById("main-drawer");
  const search = document.getElementById("main-search");
  const backdrop = document.getElementById("backdrop");
  const modalCloseBtn = document.querySelector(".no-modal__close");
  const drawerOpenBtn = header.querySelector(".no-header__toggle");
  const drawerCenter = drawer?.querySelector(".no-drawer__center") ?? null;
  const desktopNav = header.querySelector(".no-header__nav");
  const desktopNavMedia = window.matchMedia("(min-width: 1025px)");
  const desktopGnb = header.querySelector(".no-header__gnb");
  const desktopGnbIndicator = header.querySelector(".no-header__gnb-indicator");
  const desktopSubmenus = Array.from(
    header.querySelectorAll(".no-header__submenu"),
  );
  desktopNav?.setAttribute("data-expanded", "false");

  let drawerTl = null;
  let drawerMenuTextTween = null;
  let searchTl = null;
  let backdropTl = null;
  let submenuResizeObserver = null;
  let headerNavigationInFlight = false;
  let desktopGnbIndicatorSyncFrame = 0;
  let desktopSubmenuCloseTimer = 0;
  let desktopSubmenuMasterTl = null;
  let headerChromeTween = null;
  let headerBaseHeight = 0;

  const syncDesktopSubmenuHeights = () => {
    if (!desktopNav) return;

    const submenus = desktopNav.querySelectorAll(".no-header__submenu");
    let maxSubmenuHeight = 0;

    submenus.forEach((submenu) => {
      const submenuStyles = window.getComputedStyle(submenu);
      const marginTop = Number.parseFloat(submenuStyles.marginTop) || 0;
      const submenuHeight = submenu.scrollHeight;

      maxSubmenuHeight = Math.max(maxSubmenuHeight, submenuHeight + marginTop);
    });

    header.style.setProperty(
      "--header-nav-extra-height",
      `${maxSubmenuHeight}px`,
    );
  };

  const clearDesktopSubmenuCloseTimer = () => {
    if (!desktopSubmenuCloseTimer) return;
    window.clearTimeout(desktopSubmenuCloseTimer);
    desktopSubmenuCloseTimer = 0;
  };

  const syncHeaderBaseHeight = () => {
    const headerInner = header.querySelector(".no-header__inner");
    headerBaseHeight =
      headerInner?.getBoundingClientRect().height ||
      header.getBoundingClientRect().height ||
      headerBaseHeight ||
      0;
  };

  const getHeaderBaseHeight = () => headerBaseHeight;

  const getHeaderNavExtraHeight = () =>
    Number.parseFloat(
      window
        .getComputedStyle(header)
        .getPropertyValue("--header-nav-extra-height"),
    ) || 0;

  const isDesktopNavExpanded = () =>
    Boolean(
      desktopNav &&
      desktopNavMedia.matches &&
        desktopNav.getAttribute("data-expanded") === "true",
    );

  const getHeaderChromeTarget = (expanded = isDesktopNavExpanded()) => {
    const baseHeight = getHeaderBaseHeight();

    if (expanded) {
      return {
        height: baseHeight + getHeaderNavExtraHeight(),
        surfaceOpacity: 0.06,
        surfaceBlur: "24px",
        shadowOpacity: 0.08,
      };
    }

    if (window.scrollY > HEADER_BACKGROUND_THRESHOLD) {
      return {
        height: baseHeight,
        surfaceOpacity: 0.06,
        surfaceBlur: "24px",
        shadowOpacity: 0.08,
      };
    }

    return {
      height: baseHeight,
      surfaceOpacity: 0,
      surfaceBlur: "0px",
      shadowOpacity: 0,
    };
  };

  const applyHeaderChrome = ({
    expanded,
    immediate = false,
    delay = 0,
    duration,
    ease,
  } = {}) => {
    const target = getHeaderChromeTarget(expanded);

    headerChromeTween?.kill();

    if (immediate) {
      gsap.set(header, {
        height: target.height,
        "--header-surface-opacity": target.surfaceOpacity,
        "--header-surface-blur": target.surfaceBlur,
        "--header-shadow-opacity": target.shadowOpacity,
      });
      return null;
    }

    return new Promise((resolve) => {
      headerChromeTween = gsap.to(header, {
        height: target.height,
        "--header-surface-opacity": target.surfaceOpacity,
        "--header-surface-blur": target.surfaceBlur,
        "--header-shadow-opacity": target.shadowOpacity,
        delay,
        duration: duration ?? (expanded ? 0.38 : 0.28),
        ease: ease ?? (expanded ? "power3.out" : "power2.out"),
        overwrite: true,
        onComplete: () => {
          headerChromeTween = null;
          resolve();
        },
      });
    });
  };

  const buildDesktopSubmenuTimeline = () => {
    if (!desktopSubmenus.length) {
      desktopSubmenuMasterTl = null;
      return;
    }

    const submenuItems = desktopSubmenus.flatMap((submenu) =>
      Array.from(submenu.querySelectorAll(".no-header__lnb-item")),
    );

    if (!submenuItems.length) {
      desktopSubmenuMasterTl = null;
      return;
    }

    desktopSubmenuMasterTl?.kill();
    desktopSubmenuMasterTl = gsap.timeline({ paused: true });
    desktopSubmenuMasterTl.to(submenuItems, {
      opacity: 1,
      y: 0,
      duration: 0.32,
      ease: "power3.out",
      stagger: {
        each: 0.045,
        from: "start",
      },
    });
  };

  const playDesktopSubmenuReveal = () => {
    if (!desktopSubmenuMasterTl) return;
    clearDesktopSubmenuCloseTimer();
    desktopNav?.classList.add("is-submenu-active");
    desktopSubmenuMasterTl.play();
  };

  const reverseDesktopSubmenuReveal = () => {
    if (!desktopSubmenuMasterTl || !desktopNav) {
      desktopNav?.classList.remove("is-submenu-active");
      return Promise.resolve();
    }

    clearDesktopSubmenuCloseTimer();
    return new Promise((resolve) => {
      let settled = false;
      const previousReverseComplete =
        desktopSubmenuMasterTl.eventCallback("onReverseComplete");

      const finish = () => {
        if (settled) return;
        settled = true;
        window.clearTimeout(timerId);
        desktopNav.classList.remove("is-submenu-active");
        desktopSubmenuCloseTimer = 0;
        desktopSubmenuMasterTl.eventCallback(
          "onReverseComplete",
          previousReverseComplete ?? null,
        );
        if (typeof previousReverseComplete === "function") {
          previousReverseComplete();
        }
        resolve();
      };

      const timerId = window.setTimeout(finish, 360);

      desktopSubmenuMasterTl.eventCallback("onReverseComplete", finish);

      if (desktopSubmenuMasterTl.progress() <= 0) {
        finish();
        return;
      }

      desktopSubmenuMasterTl.reverse();
    });
  };

  const setDesktopNavExpanded = (expanded) => {
    if (!desktopNav || !desktopNavMedia.matches) return;
    syncDesktopSubmenuHeights();
    desktopNav.setAttribute("data-expanded", expanded ? "true" : "false");
    if (expanded) {
      header.classList.add("is-nav-expanded");
      playDesktopSubmenuReveal();
      openBackdrop();
      return applyHeaderChrome({ expanded: true });
    }
    const submenuReversePromise = reverseDesktopSubmenuReveal();
    closeBackdrop();
    const collapsePromise = submenuReversePromise.then(() =>
      applyHeaderChrome({
        expanded: false,
        duration: 0.3,
        ease: "power2.out",
      }),
    );
    collapsePromise.finally(() => {
      header.classList.remove("is-nav-expanded");
    });
    return collapsePromise;
  };

  const getDesktopActiveLink = () =>
    desktopGnb?.querySelector(".no-header__gnb-link.is-active") ?? null;

  const moveDesktopGnbIndicator = (link) => {
    if (!desktopNav || !desktopGnbIndicator || !desktopNavMedia.matches) return;

    if (!link) {
      desktopGnbIndicator.classList.remove("is-visible");
      return;
    }

    const label = link.querySelector(".no-header__gnb-label");
    const target = label instanceof HTMLElement ? label : link;
    const navRect = desktopNav.getBoundingClientRect();
    const targetRect = target.getBoundingClientRect();
    const offsetLeft = targetRect.left - navRect.left - 4;
    const width = targetRect.width + 8;

    desktopGnbIndicator.style.transform = `translateX(${offsetLeft}px)`;
    desktopGnbIndicator.style.width = `${width}px`;
    desktopGnbIndicator.classList.add("is-visible");
  };

  const syncDesktopGnbIndicator = () => {
    if (!desktopNavMedia.matches) {
      desktopGnbIndicator?.classList.remove("is-visible");
      return;
    }

    moveDesktopGnbIndicator(getDesktopActiveLink());
  };

  const scheduleDesktopGnbIndicatorSync = () => {
    if (!desktopGnbIndicator) return;

    if (desktopGnbIndicatorSyncFrame) {
      window.cancelAnimationFrame(desktopGnbIndicatorSyncFrame);
    }

    desktopGnbIndicatorSyncFrame = window.requestAnimationFrame(() => {
      desktopGnbIndicatorSyncFrame = window.requestAnimationFrame(() => {
        desktopGnbIndicatorSyncFrame = 0;
        syncDesktopGnbIndicator();
      });
    });
  };

  if (desktopNav) {
    const handleDesktopNavClose = () => {
      setDesktopNavExpanded(false);
      syncDesktopGnbIndicator();
    };
    const handleDesktopNavOpen = () => setDesktopNavExpanded(true);

    desktopNav.addEventListener("mouseenter", handleDesktopNavOpen);
    header.addEventListener("mouseleave", handleDesktopNavClose);
    desktopNav.addEventListener("focusin", handleDesktopNavOpen);
    desktopNav.addEventListener("focusout", (event) => {
      if (
        event.relatedTarget instanceof Node &&
        header.contains(event.relatedTarget)
      ) {
        return;
      }
      handleDesktopNavClose();
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        handleDesktopNavClose();
      }
    });

    syncHeaderBaseHeight();
    syncDesktopSubmenuHeights();
    buildDesktopSubmenuTimeline();
    syncDesktopGnbIndicator();

    desktopGnb?.querySelectorAll(".no-header__gnb-link").forEach((link) => {
      link.addEventListener("mouseenter", () => {
        if (!desktopNavMedia.matches) return;
        moveDesktopGnbIndicator(link);
      });

      link.addEventListener("focus", () => {
        if (!desktopNavMedia.matches) return;
        moveDesktopGnbIndicator(link);
      });
    });

    desktopGnb?.querySelectorAll(".no-header__lnb-link").forEach((link) => {
      const parentGnbItem = link.closest(".no-header__gnb-item");
      const parentGnbLink = parentGnbItem?.querySelector(
        ".no-header__gnb-link",
      );

      if (!(parentGnbLink instanceof HTMLElement)) return;

      link.addEventListener("mouseenter", () => {
        if (!desktopNavMedia.matches) return;
        moveDesktopGnbIndicator(parentGnbLink);
      });

      link.addEventListener("focus", () => {
        if (!desktopNavMedia.matches) return;
        moveDesktopGnbIndicator(parentGnbLink);
      });
    });

    if ("ResizeObserver" in window) {
      submenuResizeObserver = new ResizeObserver(() => {
        syncDesktopSubmenuHeights();
        buildDesktopSubmenuTimeline();
        syncDesktopGnbIndicator();
      });

      desktopNav.querySelectorAll(".no-header__submenu").forEach((submenu) => {
        submenuResizeObserver.observe(submenu);
      });
    }

    window.addEventListener("resize", () => {
      syncHeaderBaseHeight();
      syncDesktopSubmenuHeights();
      buildDesktopSubmenuTimeline();
      syncDesktopGnbIndicator();
      applyHeaderChrome({ immediate: true });
    });
    window.addEventListener(
      "load",
      () => {
        syncHeaderBaseHeight();
        syncDesktopSubmenuHeights();
        buildDesktopSubmenuTimeline();
        syncDesktopGnbIndicator();
        applyHeaderChrome({ immediate: true });
      },
      { once: true },
    );

    desktopNavMedia.addEventListener("change", (event) => {
      if (!event.matches) {
        clearDesktopSubmenuCloseTimer();
        desktopNav.classList.remove("is-submenu-active");
        desktopNav.setAttribute("data-expanded", "false");
        header.classList.remove("is-nav-expanded");
        desktopGnbIndicator?.classList.remove("is-visible");
        desktopSubmenuMasterTl?.pause(0);
        applyHeaderChrome({ immediate: true });
        return;
      }

      syncDesktopSubmenuHeights();
      syncHeaderBaseHeight();
      buildDesktopSubmenuTimeline();
      syncDesktopGnbIndicator();
      applyHeaderChrome({ immediate: true });
    });
  }

  if (backdrop) {
    backdropTl = gsap.timeline({ paused: true });
    backdropTl.to(backdrop, {
      opacity: 1,
      ease: "power2.out",
      duration: 0.22,
    });
    backdropTl.eventCallback("onReverseComplete", () => {
      backdrop.classList.remove("is-active");
      backdrop.setAttribute("aria-hidden", "true");
    });
  }

  if (drawer) {
    drawerTl = gsap.timeline({ paused: true });
    drawerTl.to(drawer, {
      x: "0%",
      opacity: 1,
      ease: "power3.out",
      duration: 0.24,
    });
    drawerTl.eventCallback("onReverseComplete", () => {
      drawer.classList.remove("is-active");
    });
  }

  if (search) {
    searchTl = gsap.timeline({ paused: true });
    searchTl.to(search, {
      opacity: 1,
      ease: "power3.out",
      duration: 0.24,
    });
    searchTl.eventCallback("onReverseComplete", () => {
      search.classList.remove("is-active");
      search.classList.remove("is-open");
    });
  }

  const openBackdrop = () => {
    if (!backdrop || !backdropTl) return;
    backdrop.setAttribute("aria-hidden", "false");
    backdrop.classList.add("is-active");
    backdropTl.play();
  };

  const waitForHeaderTransition = (propertyName, timeout = 320) =>
    new Promise((resolve) => {
      let settled = false;

      const finish = () => {
        if (settled) return;
        settled = true;
        header.removeEventListener("transitionend", handleTransitionEnd);
        window.clearTimeout(timerId);
        resolve();
      };

      const handleTransitionEnd = (event) => {
        if (event.target !== header) return;
        if (propertyName && event.propertyName !== propertyName) return;
        finish();
      };

      const timerId = window.setTimeout(finish, timeout);
      header.addEventListener("transitionend", handleTransitionEnd);
    });

  const waitForTimelineReverse = (timeline, timeout = 320) =>
    new Promise((resolve) => {
      if (!timeline) {
        resolve();
        return;
      }

      let settled = false;
      const previous = timeline.eventCallback("onReverseComplete");

      const finish = () => {
        if (settled) return;
        settled = true;
        window.clearTimeout(timerId);
        timeline.eventCallback("onReverseComplete", previous ?? null);
        if (typeof previous === "function") {
          previous();
        }
        resolve();
      };

      const timerId = window.setTimeout(finish, timeout);
      timeline.eventCallback("onReverseComplete", finish);
      timeline.reverse();
    });

  const closeBackdrop = (force = false) => {
    if (!backdrop || !backdropTl) return;
    if (force) {
      backdropTl.reverse();
      return;
    }
    const drawerOpen = drawer && drawer.getAttribute("data-state") === "open";
    const searchOpen = search && search.getAttribute("data-state") === "open";
    const navExpanded =
      desktopNav &&
      desktopNavMedia.matches &&
      desktopNav.getAttribute("data-expanded") === "true";
    if (!drawerOpen && !searchOpen && !navExpanded) {
      backdropTl.reverse();
    }
  };

  const openDrawer = () => {
    if (!drawer || !drawerTl) return;
    lockGlobalScroll("drawer");

    drawer.setAttribute("data-state", "open");
    drawer.setAttribute("aria-hidden", "false");
    drawer.classList.add("is-active");
    if (drawerCenter) {
      drawerCenter.scrollTop = 0;
    }
    drawerTl.play();
    openBackdrop();
    if (drawerOpenBtn) {
      drawerOpenBtn.setAttribute("aria-expanded", "true");
    }
    if (modalCloseBtn) {
      modalCloseBtn.classList.add("is-visible");
    }

    if (window.innerWidth <= 1024) {
      const drawerTextTargets = drawer.querySelectorAll(
        ".no-drawer__gnb-item, .no-drawer__lnb-item, .no-drawer__cta-wrap",
      );

      drawerMenuTextTween?.kill();
      gsap.set(drawerTextTargets, {
        opacity: 0,
        y: 24,
      });
      drawerMenuTextTween = gsap.to(drawerTextTargets, {
        opacity: 1,
        y: 0,
        duration: 0.52,
        ease: "power3.out",
        stagger: 0.055,
        delay: 0.08,
        overwrite: true,
      });
    }
  };

  const closeDrawer = ({ wait = false } = {}) => {
    if (!drawer || !drawerTl) return;
    const waitPromise = wait ? waitForTimelineReverse(drawerTl) : null;
    drawerTl.reverse();
    drawer.setAttribute("data-state", "closed");
    drawer.setAttribute("aria-hidden", "true");
    if (drawerOpenBtn) {
      drawerOpenBtn.setAttribute("aria-expanded", "false");
    }
    unlockGlobalScroll("drawer");

    closeBackdrop();
    if (modalCloseBtn) {
      modalCloseBtn.classList.remove("is-visible");
    }
    drawerMenuTextTween?.kill();
    return waitPromise;
  };

  const openSearch = () => {
    if (!search || !searchTl) return;
    lockGlobalScroll("search");

    search.setAttribute("data-state", "open");
    search.classList.add("is-active");
    search.classList.add("is-open");
    searchTl.play();
    openBackdrop();
    const searchCloseBtn = search.querySelector(".no-modal__close");
    if (searchCloseBtn) {
      searchCloseBtn.classList.add("is-visible");
    }
    if (modalCloseBtn) {
      modalCloseBtn.classList.add("is-visible");
    }
    const searchInput = search.querySelector(".no-search__input");
    if (searchInput && window.innerWidth > 1024) {
      setTimeout(() => {
        searchInput.focus();
      }, 100);
    }
  };

  const closeSearch = ({ wait = false } = {}) => {
    if (!search || !searchTl) return;
    const waitPromise = wait ? waitForTimelineReverse(searchTl) : null;
    searchTl.reverse();
    search.setAttribute("data-state", "closed");
    unlockGlobalScroll("search");

    closeBackdrop();
    const searchCloseBtn = search.querySelector(".no-modal__close");
    if (searchCloseBtn) {
      searchCloseBtn.classList.remove("is-visible");
    }
    if (modalCloseBtn) {
      modalCloseBtn.classList.remove("is-visible");
    }
    return waitPromise;
  };

  const closeHeaderUiBeforeNavigation = async () => {
    const tasks = [];
    const desktopNavExpanded =
      desktopNav &&
      desktopNavMedia.matches &&
      desktopNav.getAttribute("data-expanded") === "true";
    const drawerOpen = drawer && drawer.getAttribute("data-state") === "open";
    const searchOpen = search && search.getAttribute("data-state") === "open";
    const headerHidden = header.classList.contains("is-hidden");

    if (desktopNavExpanded) {
      // 서브메뉴 숨김만 기다리고, 크롬 높이 애니메이션은 비차단으로 실행한다.
      // setDesktopNavExpanded(false)의 반환 체인에는 applyHeaderChrome Promise가
      // 포함되어 있는데, 스크롤 핸들러 등이 중간에 headerChromeTween.kill()을
      // 호출하면 onComplete가 영구 미호출되어 navigation이 멈추는 버그가 있다.
      const submenuDone = reverseDesktopSubmenuReveal();
      closeBackdrop();
      desktopNav.setAttribute("data-expanded", "false");
      submenuDone
        .then(() =>
          applyHeaderChrome({
            expanded: false,
            duration: 0.3,
            ease: "power2.out",
          }),
        )
        .finally(() => header.classList.remove("is-nav-expanded"));
      tasks.push(submenuDone);
    }

    if (drawerOpen) {
      const drawerPromise = closeDrawer({ wait: true });
      if (drawerPromise) {
        tasks.push(drawerPromise);
      }
    }

    if (searchOpen) {
      const searchPromise = closeSearch({ wait: true });
      if (searchPromise) {
        tasks.push(searchPromise);
      }
    }

    if (headerHidden) {
      header.classList.remove("is-hidden");
      tasks.push(waitForHeaderTransition("transform"));
    }

    if (!tasks.length) {
      return false;
    }

    // 500ms 하드 타임아웃: 어떤 경로에서든 hang해도 네비게이션을 보장한다.
    const timeout = new Promise((resolve) => window.setTimeout(resolve, 500));
    await Promise.race([Promise.all(tasks), timeout]);
    return true;
  };

  const isInterceptableHeaderNavigation = (link, event) => {
    if (!(link instanceof HTMLAnchorElement) || !link.href) return false;
    if (event.defaultPrevented) return false;
    if (event.button !== 0) return false;
    if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
      return false;
    }
    if (link.hasAttribute("download")) return false;
    if ((link.getAttribute("target") || "").toLowerCase() === "_blank") {
      return false;
    }

    const url = new URL(link.href, window.location.href);
    if (url.origin !== window.location.origin) return false;
    if (url.pathname === window.location.pathname && url.hash) return false;

    return true;
  };

  document.addEventListener("click", async (event) => {
    const link = event.target.closest("a");

    if (!isInterceptableHeaderNavigation(link, event)) {
      return;
    }

    const hasEphemeralHeaderUi =
      (desktopNav &&
        desktopNavMedia.matches &&
        desktopNav.getAttribute("data-expanded") === "true") ||
      (drawer && drawer.getAttribute("data-state") === "open") ||
      (search && search.getAttribute("data-state") === "open") ||
      header.classList.contains("is-hidden");

    if (!hasEphemeralHeaderUi || headerNavigationInFlight) {
      return;
    }

    event.preventDefault();
    headerNavigationInFlight = true;

    try {
      await closeHeaderUiBeforeNavigation();
      window.location.assign(link.href);
    } finally {
      window.setTimeout(() => {
        headerNavigationInFlight = false;
      }, 400);
    }
  });

  if (drawer && drawerTl) {
    if (drawerOpenBtn) {
      drawerOpenBtn.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();

        if (search && search.getAttribute("data-state") === "open") {
          closeSearch();
          setTimeout(() => {
            openDrawer();
          }, 300);
        } else {
          openDrawer();
        }
      });
    }
  }

  if (search && searchTl) {
    const searchOpenBtn = header.querySelector(".no-header__search-toggle");

    if (searchOpenBtn) {
      searchOpenBtn.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();

        if (drawer && drawer.getAttribute("data-state") === "open") {
          closeDrawer();
          setTimeout(() => {
            openSearch();
          }, 300);
        } else {
          openSearch();
        }
      });
    }
  }

  if (backdrop) {
    backdrop.addEventListener("click", (e) => {
      if (e.target === backdrop) {
        if (search && search.getAttribute("data-state") === "open") {
          closeSearch();
        } else if (drawer && drawer.getAttribute("data-state") === "open") {
          closeDrawer();
        }
      }
    });
  }

  if (modalCloseBtn) {
    modalCloseBtn.addEventListener("click", () => {
      if (search && search.getAttribute("data-state") === "open") {
        closeSearch();
      } else if (drawer && drawer.getAttribute("data-state") === "open") {
        closeDrawer();
      }
    });
  }

  if (search) {
    const searchCloseBtn = search.querySelector(".no-modal__close");
    if (searchCloseBtn) {
      searchCloseBtn.addEventListener("click", () => {
        closeSearch();
      });
    }
  }

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      if (search && search.getAttribute("data-state") === "open") {
        closeSearch();
      } else if (drawer && drawer.getAttribute("data-state") === "open") {
        closeDrawer();
      }
    }
  });

  const heroSection = document.querySelector(".no-main-hero");
  if (heroSection) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            header.classList.add("visual");
          } else {
            header.classList.remove("visual");
          }
        });
      },
      {
        threshold: 0.1,
        rootMargin: "0px",
      },
    );

    observer.observe(heroSection);
  }

  let lastScrollY = Math.max(0, window.scrollY || window.pageYOffset || 0);
  const HEADER_BACKGROUND_THRESHOLD = 80;
  const SCROLL_THRESHOLD = 5;

  const syncHeaderScrollState = ({ immediate = false } = {}) => {
    const shouldBeScrolled = window.scrollY > HEADER_BACKGROUND_THRESHOLD;
    const wasScrolled = header.classList.contains("is-scrolled");

    header.classList.toggle("is-scrolled", shouldBeScrolled);

    if (shouldBeScrolled === wasScrolled || isDesktopNavExpanded()) {
      return;
    }

    applyHeaderChrome({ immediate });
  };

  const handleHeaderScroll = () => {
    const rawScrollY = window.scrollY || window.pageYOffset || 0;
    const currentScrollY = Math.max(0, rawScrollY);
    const delta = currentScrollY - lastScrollY;

    syncHeaderScrollState();

    // iOS overscroll bounce near top can spike/flip delta; keep header visible.
    if (currentScrollY <= HEADER_BACKGROUND_THRESHOLD + SCROLL_THRESHOLD) {
      header.classList.remove("is-hidden");
      lastScrollY = currentScrollY;
      return;
    }

    if (Math.abs(delta) < SCROLL_THRESHOLD) return;

    if (delta > 0) {
      header.classList.add("is-hidden");
    } else {
      header.classList.remove("is-hidden");
    }

    lastScrollY = currentScrollY;
  };

  window.addEventListener("scroll", handleHeaderScroll, { passive: true });
  syncHeaderBaseHeight();
  syncHeaderScrollState({ immediate: true });

  const restoreHeaderAfterSnapshot = () => {
    header.style.transition = "";
    header.style.height = "";
    header.classList.remove("is-transition-freeze");
  };

  const freezeHeaderForSnapshot = () => {
    // View Transition 직전에는 "접기"보다 "현재 상태 유지"가 중요하다.
    // 펼쳐진 GNB를 이 시점에 닫아버리면 old snapshot이 먼저 붕괴해 보인다.
    backdropTl?.kill();
    drawerTl?.kill();
    searchTl?.kill();
    headerChromeTween?.kill();

    header.style.transition = "none";
    header.style.height = `${header.offsetHeight}px`;
    header.classList.add("is-transition-freeze");

    // 강제 리플로우로 현재 펼쳐진 높이와 레이아웃을 snapshot에 고정한다.
    void header.offsetHeight;
  };

  const normalizeEphemeralUi = () => {
    if (desktopNav) {
      clearDesktopSubmenuCloseTimer();
      desktopNav.classList.remove("is-submenu-active");
      desktopSubmenuMasterTl?.pause(0);
      desktopNav.setAttribute("data-expanded", "false");
    }
    header.classList.remove("is-nav-expanded", "is-hidden");

    if (backdrop) {
      backdropTl?.pause(0);
      backdrop.classList.remove("is-active");
      backdrop.setAttribute("aria-hidden", "true");
    }

    if (drawer) {
      drawerTl?.pause(0);
      drawer.classList.remove("is-active");
      drawer.setAttribute("data-state", "closed");
      drawer.setAttribute("aria-hidden", "true");
      drawerOpenBtn?.setAttribute("aria-expanded", "false");
    }

    if (search) {
      searchTl?.pause(0);
      search.classList.remove("is-active");
      search.setAttribute("data-state", "closed");
      search.classList.remove("is-open");
    }

    ["drawer", "search", "apply-result-modal"].forEach((lockKey) => {
      unlockGlobalScroll(lockKey);
    });
    modalCloseBtn?.classList.remove("is-visible");
    applyHeaderChrome({ immediate: true });
  };

  const handlePageSwap = () => {
    freezeHeaderForSnapshot();
  };

  const handlePageReveal = () => {
    normalizeEphemeralUi();
    restoreHeaderAfterSnapshot();
    syncHeaderScrollState();
    applyHeaderChrome({ immediate: true });
    scheduleDesktopGnbIndicatorSync();

    document.fonts?.ready.then(() => {
      scheduleDesktopGnbIndicatorSync();
    });
  };

  window.addEventListener("pageswap", handlePageSwap);
  window.addEventListener("pagereveal", handlePageReveal);
  window.addEventListener("pageshow", handlePageReveal);
}


function initContactBannerParallax() {
  const sections = document.querySelectorAll(".no-contact-banner");
  if (
    !sections.length ||
    window.matchMedia("(prefers-reduced-motion: reduce)").matches
  ) {
    return;
  }

  sections.forEach((section) => {
    if (
      !(section instanceof HTMLElement) ||
      section.dataset.bannerParallaxBound === "1"
    ) {
      return;
    }

    const media = section.querySelector(".no-contact-banner__media");
    const mediaImage = media?.querySelector("img");
    if (
      !(media instanceof HTMLElement) ||
      !(mediaImage instanceof HTMLImageElement)
    ) {
      return;
    }

    section.dataset.bannerParallaxBound = "1";

    gsap.fromTo(
      mediaImage,
      {
        yPercent: -6,
        scale: 1.16,
      },
      {
        yPercent: 6,
        scale: 1.24,
        ease: "none",
        scrollTrigger: {
          trigger: section,
          start: "top bottom",
          end: "bottom top",
          scrub: 1,
          invalidateOnRefresh: true,
        },
      },
    );
  });
}

function splitTextToChars(element, className) {
  if (!(element instanceof HTMLElement)) {
    return [];
  }

  if (element.dataset.charSplitReady === "1") {
    return Array.from(element.querySelectorAll(`.${className}`));
  }

  const fragment = document.createDocumentFragment();
  const lines = [];
  let currentLine = "";

  const appendChars = (value) => {
    Array.from(value).forEach((char) => {
      const wrap = document.createElement("span");
      const span = document.createElement("span");
      wrap.className = `${className}-wrap`;
      span.className = className;
      span.setAttribute("aria-hidden", "true");
      span.textContent = char === " " ? "\u00A0" : char;
      wrap.appendChild(span);
      fragment.appendChild(wrap);
    });
  };

  Array.from(element.childNodes).forEach((node) => {
    if (node.nodeType === Node.TEXT_NODE) {
      currentLine += node.textContent ?? "";
      return;
    }

    if (node.nodeType === Node.ELEMENT_NODE && node.nodeName === "BR") {
      lines.push(currentLine);
      currentLine = "";
      return;
    }

    currentLine += node.textContent ?? "";
  });
  lines.push(currentLine);

  const normalizedLines = lines
    .map((line) => line.replace(/\s+/g, " ").trim())
    .filter(Boolean);

  normalizedLines.forEach((line, index) => {
    appendChars(line);

    if (index < normalizedLines.length - 1) {
      fragment.appendChild(document.createElement("br"));
    }
  });

  element.setAttribute("aria-label", normalizedLines.join(" "));
  element.replaceChildren(fragment);
  element.dataset.charSplitReady = "1";

  return Array.from(element.querySelectorAll(`.${className}`));
}

function createTriggerOnEnter({
  trigger,
  start,
  onEnter,
  once = true,
  invalidateOnRefresh = true,
}) {
  if (
    !(trigger instanceof HTMLElement) ||
    typeof start !== "string" ||
    typeof onEnter !== "function"
  ) {
    return;
  }

  let hasEntered = false;

  ScrollTrigger.create({
    trigger,
    start,
    once,
    invalidateOnRefresh,
    onEnter: () => {
      if (hasEntered) {
        return;
      }

      hasEntered = true;
      onEnter();
    },
  });
}

const MAIN_ARCHITECTURE_REVEAL_START = "top 66%";
const MAIN_TITLE_REVEAL_START = "top 70%";
const MAIN_CASE_CARD_ACCENT_START = "top 82%";
const ABOUT_SECTION_REVEAL_START = "top 74%";
const SUB_VISUAL_REVEAL_START = "top 78%";
const SUB_SECTION_REVEAL_START = "top 76%";

function collectRevealTargets(section, selectors) {
  return selectors.flatMap((selector) =>
    Array.from(section.querySelectorAll(selector)).filter(
      (element) => element instanceof HTMLElement,
    ),
  );
}

function bindSectionReveal({
  section,
  boundKey,
  start = SUB_SECTION_REVEAL_START,
  selectors = [],
  resolveTargets,
  stagger = 0.12,
  duration = 0.72,
  ease = "power3.out",
  revealedClass = "is-revealed",
}) {
  if (!(section instanceof HTMLElement)) {
    return;
  }

  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
    section.classList.add(revealedClass);
    return;
  }

  if (section.dataset[boundKey] === "1") {
    return;
  }

  const targets =
    typeof resolveTargets === "function"
      ? resolveTargets(section)
      : collectRevealTargets(section, selectors);

  const filteredTargets = Array.from(targets).filter(
    (element) => element instanceof HTMLElement && !element.hidden,
  );

  if (!filteredTargets.length) {
    section.classList.add(revealedClass);
    return;
  }

  section.dataset[boundKey] = "1";

  createTriggerOnEnter({
    trigger: section,
    start,
    onEnter: () => {
      gsap.to(filteredTargets, {
        autoAlpha: 1,
        y: 0,
        duration,
        ease,
        stagger,
        onComplete: () => {
          section.classList.add(revealedClass);
        },
      });
    },
  });
}


function initApplyFormReveal() {
  document.querySelectorAll(".no-apply-form").forEach((section) => {
    if (!(section instanceof HTMLElement)) {
      return;
    }

    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
      section.classList.add("is-revealed");
      return;
    }

    if (section.dataset.applyFormRevealBound === "1") {
      return;
    }

    const background = section.querySelector(".no-apply-form__bg");
    const title = section.querySelector(".no-title-txt");
    const form = section.querySelector(".no-apply-form__form");
    const blocks = collectRevealTargets(section, [
      ".no-apply-form__fields > *",
      ".no-apply-form__footer",
    ]);

    if (!(title instanceof HTMLElement) || !(form instanceof HTMLElement)) {
      section.classList.add("is-revealed");
      return;
    }

    section.dataset.applyFormRevealBound = "1";

    createTriggerOnEnter({
      trigger: section,
      start: SUB_SECTION_REVEAL_START,
      onEnter: () => {
        const timeline = gsap.timeline({
          defaults: {
            ease: "power3.out",
          },
          onComplete: () => {
            section.classList.add("is-revealed");
          },
        });

        if (background instanceof HTMLElement) {
          timeline.to(
            background,
            {
              autoAlpha: 1,
              scale: 1,
              duration: 1.02,
            },
            0,
          );
        }

        timeline
          .to(
            [title, form],
            {
              autoAlpha: 1,
              y: 0,
              duration: 0.72,
              stagger: 0.12,
            },
            0.08,
          )
          .to(
            blocks,
            {
              autoAlpha: 1,
              y: 0,
              duration: 0.6,
              stagger: 0.06,
            },
            0.24,
          );
      },
    });
  });
}

function initApplyFormInteraction() {
  const form = document.querySelector(".no-apply-form__form");
  const modal = document.querySelector(".no-apply-result-modal");

  if (!(form instanceof HTMLFormElement) || !(modal instanceof HTMLElement)) {
    return;
  }

  if (form.dataset.applyInteractionBound === "1") {
    return;
  }

  form.dataset.applyInteractionBound = "1";

  const phoneInput = form.querySelector('input[name="phone"]');
  const submitButton = form.querySelector('[type="submit"]');
  const modalPanel = modal.querySelector(".no-apply-result-modal__panel");
  const modalTitle = modal.querySelector(".no-apply-result-modal__title");
  const modalMessage = modal.querySelector(".no-apply-result-modal__message");
  const closeButtons = modal.querySelectorAll("[data-apply-modal-close]");

  const formatPhoneNumber = (value) => {
    const digits = String(value ?? "")
      .replace(/\D/g, "")
      .slice(0, 11);

    if (digits.startsWith("02")) {
      if (digits.length <= 2) return digits;
      if (digits.length <= 5) return `${digits.slice(0, 2)}-${digits.slice(2)}`;
      if (digits.length <= 9) {
        return `${digits.slice(0, 2)}-${digits.slice(2, 5)}-${digits.slice(5)}`;
      }
      return `${digits.slice(0, 2)}-${digits.slice(2, 6)}-${digits.slice(6, 10)}`;
    }

    if (digits.length <= 3) return digits;
    if (digits.length <= 7) return `${digits.slice(0, 3)}-${digits.slice(3)}`;
    return `${digits.slice(0, 3)}-${digits.slice(3, 7)}-${digits.slice(7, 11)}`;
  };

  const setSubmitting = (submitting) => {
    if (!(submitButton instanceof HTMLButtonElement)) return;
    submitButton.disabled = submitting;
    submitButton.classList.toggle("is-loading", submitting);
  };

  const openModal = ({ success, title, message }) => {
    modal.dataset.resultStatus = success ? "success" : "error";
    modal.classList.toggle("is-success", success);
    modal.classList.toggle("is-error", !success);
    modal.setAttribute("aria-hidden", "false");

    if (modalTitle) {
      modalTitle.textContent = title;
    }

    if (modalMessage) {
      modalMessage.textContent = message;
    }

    lockGlobalScroll("apply-result-modal");

    gsap
      .timeline({
        defaults: {
          ease: "power3.out",
        },
      })
      .to(modal, {
        autoAlpha: 1,
        duration: 0.24,
      })
      .to(
        modalPanel,
        {
          autoAlpha: 1,
          y: 0,
          scale: 1,
          duration: 0.42,
        },
        "-=0.1",
      );

    if (modalPanel instanceof HTMLElement) {
      modalPanel.focus({ preventScroll: true });
    }
  };

  const closeModal = () => {
    gsap
      .timeline({
        defaults: {
          ease: "power2.out",
        },
        onComplete: () => {
          modal.setAttribute("aria-hidden", "true");
          delete modal.dataset.resultStatus;
          modal.classList.remove("is-success", "is-error");
          unlockGlobalScroll("apply-result-modal");
        },
      })
      .to(modalPanel, {
        autoAlpha: 0,
        y: 18,
        scale: 0.96,
        duration: 0.24,
      })
      .to(
        modal,
        {
          autoAlpha: 0,
          duration: 0.22,
        },
        "-=0.12",
      );
  };

  const getErrorMessage = (result) => {
    const errors =
      result?.errors && typeof result.errors === "object"
        ? Object.values(result.errors).filter(Boolean)
        : [];

    if (errors.length) {
      return String(errors[0]);
    }

    return result?.message || "필수 항목을 정확히 입력해 주세요.";
  };

  if (phoneInput instanceof HTMLInputElement) {
    phoneInput.addEventListener("input", () => {
      phoneInput.value = formatPhoneNumber(phoneInput.value);
    });

    phoneInput.addEventListener("blur", () => {
      phoneInput.value = formatPhoneNumber(phoneInput.value);
    });
  }

  closeButtons.forEach((button) => {
    button.addEventListener("click", closeModal);
  });

  document.addEventListener("keydown", (event) => {
    if (
      event.key === "Escape" &&
      modal.getAttribute("aria-hidden") === "false"
    ) {
      closeModal();
    }
  });

  form.addEventListener("submit", async (event) => {
    event.preventDefault();

    if (!form.checkValidity()) {
      form.reportValidity();
      openModal({
        success: false,
        title: "입력값을 확인해 주세요",
        message: "필수 항목을 모두 입력하고 선택해 주세요.",
      });
      return;
    }

    setSubmitting(true);

    try {
      const response = await fetch(form.action, {
        method: "POST",
        headers: {
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: new FormData(form),
      });

      const result = await response.json().catch(() => null);

      if (response.ok && result?.success) {
        form.reset();
        openModal({
          success: true,
          title: "신청이 완료되었습니다",
          message:
            result.message || "담당자가 확인 후 빠르게 연락드리겠습니다.",
        });
        return;
      }

      openModal({
        success: false,
        title: "입력값을 확인해 주세요",
        message: getErrorMessage(result),
      });
    } catch (_) {
      openModal({
        success: false,
        title: "신청을 완료하지 못했습니다",
        message: "잠시 후 다시 시도해 주세요.",
      });
    } finally {
      setSubmitting(false);
    }
  });
}


function initContactBannerReveal() {
  document.querySelectorAll(".no-contact-banner").forEach((section) => {
    if (!(section instanceof HTMLElement)) {
      return;
    }

    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
      section.classList.add("is-revealed");
      return;
    }

    if (section.dataset.contactBannerRevealBound === "1") {
      return;
    }

    const media = section.querySelector(".no-contact-banner__media");
    const contentTargets = collectRevealTargets(section, [
      ".no-contact-banner__content > *",
    ]);

    if (!(media instanceof HTMLElement) || !contentTargets.length) {
      section.classList.add("is-revealed");
      return;
    }

    section.dataset.contactBannerRevealBound = "1";

    createTriggerOnEnter({
      trigger: section,
      start: SUB_SECTION_REVEAL_START,
      onEnter: () => {
        gsap
          .timeline({
            onComplete: () => {
              section.classList.add("is-revealed");
            },
          })
          .to(media, {
            autoAlpha: 1,
            scale: 1,
            duration: 0.92,
            ease: "power3.out",
          })
          .to(
            contentTargets,
            {
              autoAlpha: 1,
              y: 0,
              duration: 0.72,
              ease: "power3.out",
              stagger: 0.12,
            },
            "-=0.56",
          );
      },
    });
  });
}

function initFaqSectionReveal() {
  document.querySelectorAll(".no-sub-faq").forEach((section) => {
    if (!(section instanceof HTMLElement)) {
      return;
    }

    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
      section.classList.add("is-revealed");
      return;
    }

    if (section.dataset.faqRevealBound === "1") {
      return;
    }

    const background = section.querySelector(".no-sub-faq__bg");
    const title = section.querySelector(".no-sub-faq__txt");
    const body = section.querySelector(".no-sub-faq__body");
    const items = Array.from(section.querySelectorAll(".no-sub-faq__item"));
    const pagination = section.querySelector(".no-sub-faq__pagination");

    if (!(title instanceof HTMLElement) || !(body instanceof HTMLElement)) {
      section.classList.add("is-revealed");
      return;
    }

    section.dataset.faqRevealBound = "1";

    createTriggerOnEnter({
      trigger: section,
      start: SUB_SECTION_REVEAL_START,
      onEnter: () => {
        const timeline = gsap.timeline({
          defaults: {
            ease: "power3.out",
          },
          onComplete: () => {
            section.classList.add("is-revealed");
          },
        });

        if (background instanceof HTMLElement) {
          timeline.to(
            background,
            {
              autoAlpha: 1,
              scale: 1,
              duration: 1.02,
            },
            0,
          );
        }

        timeline
          .to(
            [title, body],
            {
              autoAlpha: 1,
              y: 0,
              duration: 0.72,
              stagger: 0.12,
            },
            0.08,
          )
          .to(
            items,
            {
              autoAlpha: 1,
              y: 0,
              duration: 0.58,
              stagger: 0.08,
            },
            0.22,
          );

        if (pagination instanceof HTMLElement) {
          timeline.to(
            pagination,
            {
              autoAlpha: 1,
              y: 0,
              duration: 0.56,
            },
            0.36,
          );
        }
      },
    });
  });
}

function initFooterReveal() {
  const footer = document.querySelector(".no-footer");
  if (!(footer instanceof HTMLElement)) {
    return;
  }

  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
    footer.classList.add("is-revealed");
    return;
  }

  if (footer.dataset.footerRevealBound === "1") {
    return;
  }

  const logoShells = Array.from(
    footer.querySelectorAll(".no-footer__logo-shell"),
  );
  const logoMarks = Array.from(
    footer.querySelectorAll(".no-footer__logo-shell img"),
  );
  const metaBlocks = Array.from(
    footer.querySelectorAll(".no-footer__meta > *"),
  );
  const bottomBlocks = Array.from(
    footer.querySelectorAll(".no-footer__bottom > *"),
  );

  footer.dataset.footerRevealBound = "1";

  createTriggerOnEnter({
    trigger: footer,
    start: "top 92%",
    onEnter: () => {
      gsap
        .timeline({
          defaults: {
            ease: "power3.out",
          },
          onComplete: () => {
            footer.classList.add("is-revealed");
            gsap.set(
              [...logoShells, ...logoMarks, ...metaBlocks, ...bottomBlocks],
              {
                clearProps: "transform,opacity,visibility",
              },
            );
          },
        })
        .to(logoShells, {
          autoAlpha: 1,
          y: 0,
          duration: 0.62,
          stagger: 0.1,
        })
        .to(
          logoMarks,
          {
            y: 0,
            duration: 0.78,
            ease: "power4.out",
            stagger: 0.1,
          },
          0.08,
        )
        .to(
          metaBlocks,
          {
            autoAlpha: 1,
            y: 0,
            duration: 0.58,
            stagger: 0.1,
          },
          "-=0.28",
        )
        .to(
          bottomBlocks,
          {
            autoAlpha: 1,
            y: 0,
            duration: 0.54,
            stagger: 0.08,
          },
          "-=0.2",
        );
    },
  });
}

function initHeaderLightSectionObserver() {
  const header = document.getElementById("main-header");
  if (!(header instanceof HTMLElement)) return;

  const lightSections = Array.from(
    document.querySelectorAll(".no-main-media, .no-sub-media, .no-sub-cases"),
  );
  if (!lightSections.length) return;

  const intersecting = new Set();
  let observer = null;

  const createObserver = () => {
    observer?.disconnect();
    intersecting.clear();

    const headerHeight = header.offsetHeight;
    const marginBottom = Math.max(0, window.innerHeight - headerHeight);

    observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            intersecting.add(entry.target);
          } else {
            intersecting.delete(entry.target);
          }
        });
        header.classList.toggle("is-on-light-section", intersecting.size > 0);
      },
      {
        rootMargin: `0px 0px -${marginBottom}px 0px`,
        threshold: 0,
      },
    );

    lightSections.forEach((section) => observer.observe(section));
  };

  createObserver();

  let resizeTimer = 0;
  window.addEventListener("resize", () => {
    window.clearTimeout(resizeTimer);
    resizeTimer = window.setTimeout(createObserver, 200);
  });
}

function initFloatingButton() {
  const floatingButton = document.querySelector(".no-floating-button");
  if (!floatingButton) return;

  const topButton = floatingButton.querySelector(".no-floating-button__top");
  if (!topButton) return;

  topButton.addEventListener("click", () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  });

  const handleScroll = () => {
    const scrollY = window.scrollY || window.pageYOffset;
    const threshold = 300;

    const footer = document.querySelector(".no-footer");
    let footerTop = null;

    if (footer) {
      const footerRect = footer.getBoundingClientRect();
      footerTop = scrollY + footerRect.top;

      const buttonHeight = topButton.offsetHeight || 56;
      const buttonBottom = scrollY + window.innerHeight - buttonHeight - 20;

      if (buttonBottom >= footerTop) {
        topButton.classList.remove("is-visible");
        return;
      }
    }

    if (scrollY > threshold) {
      topButton.classList.add("is-visible");
    } else {
      topButton.classList.remove("is-visible");
    }
  };

  window.addEventListener("scroll", handleScroll, { passive: true });

  handleScroll();
}


function initSubMediaSwiper() {
  const swipers = document.querySelectorAll(".no-sub-media__swiper");
  if (!swipers.length || typeof window.Swiper !== "function") {
    return;
  }

  swipers.forEach((swiper) => {
    if (swiper.dataset.swiperInitialized === "1") {
      return;
    }

    const section = swiper.closest(".no-sub-media");
    const prevButton = section?.querySelector(".no-sub-media__button--prev");
    const nextButton = section?.querySelector(".no-sub-media__button--next");

    swiper.dataset.swiperInitialized = "1";

    new window.Swiper(swiper, {
      slidesPerView: 1.08,
      spaceBetween: 16,
      speed: 600,
      watchOverflow: true,
      navigation:
        prevButton && nextButton
          ? {
              prevEl: prevButton,
              nextEl: nextButton,
            }
          : undefined,
      breakpoints: {
        768: {
          slidesPerView: 2.2,
          spaceBetween: 20,
        },
        1025: {
          slidesPerView: 3,
          spaceBetween: 24,
        },
      },
    });
  });
}

function initCategorySwiper() {
  const swipers = document.querySelectorAll(".no-category__swiper");
  if (!swipers.length || typeof window.Swiper !== "function") {
    return;
  }

  swipers.forEach((swiper) => {
    if (swiper.dataset.swiperInitialized === "1") {
      return;
    }

    swiper.dataset.swiperInitialized = "1";

    new window.Swiper(swiper, {
      slidesPerView: "auto",
      spaceBetween: 8,
      speed: 680,
      slideToClickedSlide: true,
      freeMode: {
        enabled: true,
        momentum: true,
      },
      watchOverflow: true,
      breakpoints: {
        768: {
          spaceBetween: 10,
        },
        1025: {
          spaceBetween: 12,
        },
      },
    });
  });
}


function initContactLocationTabs() {
  document.querySelectorAll("[data-location-tabs]").forEach((section) => {
    const tabs = Array.from(section.querySelectorAll("[data-location-tab]"));
    const panels = Array.from(
      section.querySelectorAll("[data-location-panel]"),
    );

    if (!tabs.length || !panels.length) {
      return;
    }

    const initRoughmapInPanel = (panel) => {
      const container = panel.querySelector("[data-roughmap-timestamp]");
      if (!container || container.dataset.roughmapInitialized) return;
      container.dataset.roughmapInitialized = "true";
      new daum.roughmap.Lander({
        timestamp: container.dataset.roughmapTimestamp,
        key: container.dataset.roughmapKey,
        mapWidth: container.dataset.roughmapWidth,
        mapHeight: container.dataset.roughmapHeight,
      }).render();
    };

    const activateTab = (targetId) => {
      tabs.forEach((tab) => {
        const isActive = tab.dataset.locationTab === targetId;
        tab.classList.toggle("is-active", isActive);
        tab.setAttribute("aria-selected", isActive ? "true" : "false");
      });

      panels.forEach((panel) => {
        const isActive = panel.dataset.locationPanel === targetId;
        panel.classList.toggle("is-active", isActive);
        panel.hidden = !isActive;
        if (isActive) initRoughmapInPanel(panel);
      });
    };

    const firstActive = panels.find((p) => !p.hidden);
    if (firstActive) initRoughmapInPanel(firstActive);

    tabs.forEach((tab) => {
      tab.addEventListener("click", () => {
        activateTab(tab.dataset.locationTab);
      });
    });
  });
}


function prepareLayeredTextFill(element, baseClass, fillClass) {
  if (!(element instanceof HTMLElement)) {
    return null;
  }

  if (element.dataset.layeredFillReady === "1") {
    return element.querySelector(`.${fillClass}`);
  }

  const sourceHtml = element.innerHTML.trim();
  const text = (element.textContent ?? "").replace(/\s+/g, " ").trim();
  const base = document.createElement("span");
  const fill = document.createElement("span");

  base.className = baseClass;
  fill.className = fillClass;
  base.setAttribute("aria-hidden", "true");
  fill.setAttribute("aria-hidden", "true");
  base.innerHTML = sourceHtml;
  fill.innerHTML = sourceHtml;

  element.innerHTML = "";
  element.setAttribute("aria-label", text);
  element.append(base, fill);
  element.dataset.layeredFillReady = "1";

  return fill;
}

function prepareLayeredLineFill(element, lineClass, baseClass, fillClass) {
  if (!(element instanceof HTMLElement)) {
    return [];
  }

  if (element.dataset.layeredLineFillReady === "1") {
    return Array.from(element.querySelectorAll(`.${fillClass}`));
  }

  const sourceHtml = element.innerHTML.trim();
  const text = (element.textContent ?? "").replace(/\s+/g, " ").trim();
  const lines = sourceHtml
    .split(/<br\s*\/?>/i)
    .map((line) => line.trim())
    .filter(Boolean);

  if (!lines.length) {
    return [];
  }

  const fragment = document.createDocumentFragment();

  lines.forEach((line) => {
    const lineWrap = document.createElement("span");
    const base = document.createElement("span");
    const fill = document.createElement("span");

    lineWrap.className = lineClass;
    base.className = baseClass;
    fill.className = fillClass;
    base.setAttribute("aria-hidden", "true");
    fill.setAttribute("aria-hidden", "true");
    base.innerHTML = line;
    fill.innerHTML = line;
    lineWrap.append(base, fill);
    fragment.appendChild(lineWrap);
  });

  element.innerHTML = "";
  element.setAttribute("aria-label", text);
  element.append(fragment);
  element.dataset.layeredLineFillReady = "1";

  return Array.from(element.querySelectorAll(`.${fillClass}`));
}


function initMarquee() {
  const marquees = document.querySelectorAll("[data-marquee]");
  if (!marquees.length) return;

  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

  marquees.forEach((el) => {
    if (el.dataset.marqueeInitialized === "1") return;
    el.dataset.marqueeInitialized = "1";

    const track = el.querySelector(".no-marquee__track");
    if (!track) return;

    const speed = parseFloat(el.dataset.speed) || 28;

    gsap.to(track, {
      xPercent: -50,
      ease: "none",
      duration: speed,
      repeat: -1,
    });
  });
}


function initCharHoverBtns() {
  document.querySelectorAll(".no-btn-char").forEach((btn) => {
    if (btn.querySelector(".no-btn-char__cell")) return;

    const icon = btn.querySelector(".no-btn-char__icon");
    const raw = [...btn.childNodes]
      .filter((node) => node !== icon)
      .map((node) => node.textContent)
      .join("")
      .trim();

    if (!raw) return;

    btn.setAttribute("aria-label", raw);

    // Build char cells
    const textWrap = document.createElement("span");
    textWrap.className = "no-btn-char__text";

    [...raw].forEach((ch) => {
      const cell = document.createElement("span");
      cell.className = "no-btn-char__cell";

      const orig = document.createElement("span");
      orig.className = "no-btn-char__char no-btn-char__char--orig";
      orig.textContent = ch === " " ? "\u00A0" : ch;

      const clone = document.createElement("span");
      clone.className = "no-btn-char__char no-btn-char__char--clone";
      clone.textContent = ch === " " ? "\u00A0" : ch;

      cell.appendChild(orig);
      cell.appendChild(clone);
      textWrap.appendChild(cell);
    });

    btn.replaceChildren(textWrap);

    if (icon) {
      btn.appendChild(icon);
    }

    const origChars = textWrap.querySelectorAll(".no-btn-char__char--orig");
    const cloneChars = textWrap.querySelectorAll(".no-btn-char__char--clone");

    const tl = gsap
      .timeline({ paused: true })
      .to(
        origChars,
        {
          y: "-100%",
          duration: 0.46,
          stagger: 0.03,
          ease: "power3.inOut",
        },
        0,
      )
      .to(
        cloneChars,
        {
          y: "0%",
          duration: 0.46,
          stagger: 0.03,
          ease: "power3.inOut",
        },
        0,
      );

    btn.addEventListener("mouseenter", () => {
      tl.play();
    });

    btn.addEventListener("mouseleave", () => {
      tl.reverse();
    });
  });
}

function initHeaderGnbCharHover() {
  document
    .querySelectorAll(".no-header__gnb-link, .no-footer__menu-link")
    .forEach((link) => {
      const label = link.querySelector(
        ".no-header__gnb-label, .no-footer__menu-label",
      );

      if (!label || label.querySelector(".no-header__gnb-char-cell")) return;

      const raw = (label.textContent || "").trim();

      if (!raw) return;

      label.setAttribute("aria-label", raw);

      const textWrap = document.createElement("span");
      textWrap.className = "no-header__gnb-char-text";
      textWrap.setAttribute("aria-hidden", "true");

      [...raw].forEach((ch) => {
        const cell = document.createElement("span");
        cell.className = "no-header__gnb-char-cell";

        const orig = document.createElement("span");
        orig.className = "no-header__gnb-char no-header__gnb-char--orig";
        orig.textContent = ch === " " ? "\u00A0" : ch;

        const clone = document.createElement("span");
        clone.className = "no-header__gnb-char no-header__gnb-char--clone";
        clone.textContent = ch === " " ? "\u00A0" : ch;

        cell.appendChild(orig);
        cell.appendChild(clone);
        textWrap.appendChild(cell);
      });

      label.replaceChildren(textWrap);

      const origChars = textWrap.querySelectorAll(".no-header__gnb-char--orig");
      const cloneChars = textWrap.querySelectorAll(
        ".no-header__gnb-char--clone",
      );

      const tl = gsap
        .timeline({ paused: true })
        .to(
          origChars,
          {
            y: "-100%",
            duration: 0.46,
            stagger: 0.03,
            ease: "power3.inOut",
          },
          0,
        )
        .to(
          cloneChars,
          {
            y: "0%",
            duration: 0.46,
            stagger: 0.03,
            ease: "power3.inOut",
          },
          0,
        );

      link.addEventListener("mouseenter", () => {
        tl.play();
      });

      link.addEventListener("mouseleave", () => {
        tl.reverse();
      });

      link.addEventListener("focusin", () => {
        tl.play();
      });

      link.addEventListener("focusout", () => {
        tl.reverse();
      });
    });
}
