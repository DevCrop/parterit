export class DocumentHints {
  constructor(root = document.documentElement) {
    this.root = root;
  }

  set(key, value) {
    if (!this.root) return;
    this.root.dataset[key] = value;
  }

  init({ sameDocumentSupported, reducedMotion, crossDocumentEnabled }) {
    this.set("vtSameDoc", sameDocumentSupported ? "1" : "0");
    this.set("vtReducedMotion", reducedMotion ? "1" : "0");
    this.set("vtCrossDoc", crossDocumentEnabled ? "1" : "0");
  }

  updateReducedMotion(isReduced) {
    this.set("vtReducedMotion", isReduced ? "1" : "0");
  }
}
