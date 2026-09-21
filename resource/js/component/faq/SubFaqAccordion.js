/**
 * .no-sub-faq - single-open accordion with the first item open by default.
 */
let subFaqInstanceCount = 0;

export function initSubFaqAccordion(rootSelector = ".no-sub-faq") {
  const roots = document.querySelectorAll(rootSelector);
  if (!roots.length) return;

  roots.forEach((root) => {
    const items = Array.from(root.querySelectorAll(".no-sub-faq__item"));
    if (!items.length) return;

    const instance = ++subFaqInstanceCount;
    items.forEach((item, i) => {
      const btn = item.querySelector(".no-sub-faq__trigger");
      const panel = item.querySelector(".no-sub-faq__panel");
      if (!btn || !panel) return;
      if (!btn.id) btn.id = `sub-faq-${instance}-trigger-${i + 1}`;
      if (!panel.id) panel.id = `sub-faq-${instance}-panel-${i + 1}`;
      btn.setAttribute("aria-controls", panel.id);
      if (!panel.hasAttribute("role")) panel.setAttribute("role", "region");
      panel.setAttribute("aria-labelledby", btn.id);
    });

    const animatePanel = (panel, open) => {
      if (!(panel instanceof HTMLElement)) return;

      panel.style.transition =
        "height 0.32s ease, opacity 0.24s ease, transform 0.32s ease";
      panel.style.overflow = "hidden";

      if (open) {
        panel.hidden = false;
        panel.style.height = "0px";
        panel.style.opacity = "0";
        panel.style.transform = "translateY(-8px)";

        requestAnimationFrame(() => {
          panel.style.height = `${panel.scrollHeight}px`;
          panel.style.opacity = "1";
          panel.style.transform = "translateY(0)";
        });

        const onOpenEnd = (event) => {
          if (event.target !== panel || event.propertyName !== "height") return;
          panel.style.height = "auto";
          panel.removeEventListener("transitionend", onOpenEnd);
        };

        panel.addEventListener("transitionend", onOpenEnd);
        return;
      }

      if (panel.hidden) return;

      panel.style.height = `${panel.scrollHeight}px`;
      panel.style.opacity = "1";
      panel.style.transform = "translateY(0)";

      requestAnimationFrame(() => {
        panel.style.height = "0px";
        panel.style.opacity = "0";
        panel.style.transform = "translateY(-8px)";
      });

      const onCloseEnd = (event) => {
        if (event.target !== panel || event.propertyName !== "height") return;
        panel.hidden = true;
        panel.removeEventListener("transitionend", onCloseEnd);
      };

      panel.addEventListener("transitionend", onCloseEnd);
    };

    const syncItem = (item, open, animate = false) => {
      const btn = item.querySelector(".no-sub-faq__trigger");
      const panel = item.querySelector(".no-sub-faq__panel");
      if (!btn || !panel) return;

      item.classList.toggle("is-open", open);
      btn.setAttribute("aria-expanded", open ? "true" : "false");

      if (animate) {
        animatePanel(panel, open);
        return;
      }

      panel.hidden = !open;
      panel.style.height = open ? "auto" : "0px";
      panel.style.opacity = open ? "1" : "0";
      panel.style.transform = open ? "translateY(0)" : "translateY(-8px)";
    };

    if (!items.some((item) => item.classList.contains("is-open"))) {
      syncItem(items[0], true);
    }

    items.forEach((item) => {
      const btn = item.querySelector(".no-sub-faq__trigger");
      if (!btn) return;

      btn.addEventListener("click", () => {
        const willOpen = !item.classList.contains("is-open");

        items.forEach((other) => syncItem(other, false, true));
        syncItem(item, willOpen, true);
      });
    });
  });
}
