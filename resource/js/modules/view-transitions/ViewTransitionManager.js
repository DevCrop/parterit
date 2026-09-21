import { MotionPreference } from "./MotionPreference.js";
import { DocumentHints } from "./DocumentHints.js";
import { SameDocumentTransition } from "./SameDocumentTransition.js";
import { CrossDocumentTransition } from "./CrossDocumentTransition.js";

export class ViewTransitionManager {
  constructor({
    motionPreference = new MotionPreference(),
    documentHints = new DocumentHints(),
    sameDocumentTransition = new SameDocumentTransition(),
    crossDocumentTransition = new CrossDocumentTransition(),
  } = {}) {
    this.motionPreference = motionPreference;
    this.documentHints = documentHints;
    this.sameDocumentTransition = sameDocumentTransition;
    this.crossDocumentTransition = crossDocumentTransition;
    this.isInitialized = false;
    this.unsubscribeMotion = null;
  }

  init() {
    if (this.isInitialized) return;
    this.isInitialized = true;

    this.documentHints.init({
      sameDocumentSupported: this.supportsSameDocumentTransition(),
      reducedMotion: this.prefersReducedMotion(),
      crossDocumentEnabled: this.supportsCrossDocumentTransition(),
    });

    this.unsubscribeMotion = this.motionPreference.onChange((event) => {
      this.documentHints.updateReducedMotion(!!event.matches);
    });
  }

  destroy() {
    if (typeof this.unsubscribeMotion === "function") {
      this.unsubscribeMotion();
    }
    this.unsubscribeMotion = null;
    this.isInitialized = false;
  }

  supportsSameDocumentTransition() {
    return this.sameDocumentTransition.supports();
  }

  supportsCrossDocumentTransition() {
    return this.crossDocumentTransition.supports();
  }

  prefersReducedMotion() {
    return this.motionPreference.prefersReducedMotion();
  }

  // Standard transition — no type hints.
  transition(updateCallback) {
    return this.sameDocumentTransition.run(updateCallback, {
      reducedMotion: this.prefersReducedMotion(),
    });
  }

  // Typed transition — passes `types` to startViewTransition (Chrome 125+).
  // CSS can target types via :active-view-transition-type() selector.
  transitionWithTypes(updateCallback, types = []) {
    return this.sameDocumentTransition.run(updateCallback, {
      reducedMotion: this.prefersReducedMotion(),
      types,
    });
  }

  // The live ViewTransition object for the most recent same-document transition.
  // Useful for hooking into .ready (pre-animation) or .finished (post-animation).
  get activeTransition() {
    return this.sameDocumentTransition.current;
  }
}

export const viewTransitionManager = new ViewTransitionManager();
