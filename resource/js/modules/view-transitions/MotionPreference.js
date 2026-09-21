export class MotionPreference {
  constructor(query = "(prefers-reduced-motion: reduce)") {
    this.query = query;
    this.mql = this.createMediaQueryList();
  }

  createMediaQueryList() {
    if (typeof window.matchMedia !== "function") {
      return null;
    }

    try {
      return window.matchMedia(this.query);
    } catch (_) {
      return null;
    }
  }

  prefersReducedMotion() {
    return this.mql ? this.mql.matches : false;
  }

  onChange(handler) {
    if (!this.mql || typeof handler !== "function") {
      return () => {};
    }

    if (typeof this.mql.addEventListener === "function") {
      this.mql.addEventListener("change", handler);
      return () => this.mql.removeEventListener("change", handler);
    }

    if (typeof this.mql.addListener === "function") {
      this.mql.addListener(handler);
      return () => this.mql.removeListener(handler);
    }

    return () => {};
  }
}
