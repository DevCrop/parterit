export class SameDocumentTransition {
  #current = null;

  supports() {
    return typeof document.startViewTransition === "function";
  }

  run(updateCallback, { reducedMotion = false, types = [] } = {}) {
    if (typeof updateCallback !== "function") return Promise.resolve();

    if (!this.supports() || reducedMotion) {
      return Promise.resolve(updateCallback());
    }

    const update = () => Promise.resolve(updateCallback());

    // `types` array is supported from Chrome 125+ ({ update, types } object form).
    // Fall back to the function-only signature on older engines.
    let vt;
    if (types.length) {
      try {
        vt = document.startViewTransition({ update, types });
      } catch (_) {
        vt = document.startViewTransition(update);
      }
    } else {
      vt = document.startViewTransition(update);
    }

    this.#current = vt;
    return vt.finished;
  }

  // Expose the live ViewTransition object so callers can hook into .ready / .finished.
  get current() {
    return this.#current;
  }
}
