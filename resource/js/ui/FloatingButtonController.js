export class FloatingButtonController {
  init() {
    const floatingButton = document.querySelector(".no-floating-button");
    if (!floatingButton) return;

    const topButton = floatingButton.querySelector(".no-floating-button__top");
    if (!topButton) return;

    topButton.addEventListener("click", () => {
      window.scrollTo({ top: 0, behavior: window.matchMedia("(prefers-reduced-motion: reduce)").matches ? "instant" : "smooth" });
    });

    const handleScroll = () => {
      const scrollY = window.scrollY || window.pageYOffset;
      topButton.classList.toggle("is-visible", scrollY > 300);
      floatingButton.classList.toggle("has-top", scrollY > 300);
    };

    window.addEventListener("scroll", handleScroll, { passive: true });
    handleScroll();
  }
}
