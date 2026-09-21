export class CrossDocumentTransition {
  constructor(root = document.documentElement) {
    this.root = root;
  }

  supports() {
    return !!(
      this.root &&
      typeof CSS !== "undefined" &&
      typeof CSS.supports === "function" &&
      CSS.supports("view-transition-name: main-content")
    );
  }
}
