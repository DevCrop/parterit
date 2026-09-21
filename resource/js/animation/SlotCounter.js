const SLOT_SELECTOR = "[data-slot-value]";
const SLOT_DURATION = 1150;
const SLOT_STAGGER = 55;

export class SlotCounter {
  init() {
    const sections = document.querySelectorAll("[data-slot-section]");
    if (!sections.length) return;

    const values = Array.from(sections).flatMap((section) =>
      Array.from(section.querySelectorAll(SLOT_SELECTOR)),
    );
    if (!values.length) return;

    const showFinalValues = () => values.forEach((value) => this.#setFinalValue(value));
    if (
      window.matchMedia("(prefers-reduced-motion: reduce)").matches ||
      !("IntersectionObserver" in window) ||
      typeof Element.prototype.animate !== "function"
    ) {
      showFinalValues();
      return;
    }

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        this.#animateSection(entry.target);
        observer.unobserve(entry.target);
      });
    }, { threshold: 0.25 });

    sections.forEach((section) => observer.observe(section));
  }

  #animateSection(section) {
    section.querySelectorAll(SLOT_SELECTOR).forEach((value) => {
      if (value.dataset.slotPlayed === "1") return;
      value.dataset.slotPlayed = "1";

      const formattedValue = Number(value.dataset.slotValue).toLocaleString("ko-KR", {
        maximumFractionDigits: 1,
      });
      const fragment = document.createDocumentFragment();
      let digitIndex = 0;

      const animations = [];
      Array.from(formattedValue).forEach((character) => {
        if (!/\d/.test(character)) {
          fragment.appendChild(document.createTextNode(character));
          return;
        }

        const targetDigit = Number(character);
        const reel = document.createElement("span");
        const track = document.createElement("span");
        reel.className = "partnerit-metrics__digit";
        track.className = "partnerit-metrics__reel";
        reel.setAttribute("aria-hidden", "true");

        for (let row = 0; row < 20; row += 1) {
          const cell = document.createElement("span");
          cell.className = "partnerit-metrics__reel-cell";
          cell.textContent = String(row % 10);
          track.appendChild(cell);
        }

        reel.appendChild(track);
        fragment.appendChild(reel);
        const delay = digitIndex * SLOT_STAGGER;
        const start = `translateY(-${targetDigit * 5}%)`;
        const end = `translateY(-${(10 + targetDigit) * 5}%)`;
        requestAnimationFrame(() => {
          const animation = track.animate(
            [{ transform: start }, { transform: end }],
            { duration: SLOT_DURATION, delay, easing: "cubic-bezier(0.16, 1, 0.3, 1)", fill: "forwards" },
          );
          animations.push(animation.finished);
          if (animations.length === digitIndex) {
            Promise.all(animations).then(() => {
              value.textContent = formattedValue;
            }).catch(() => this.#setFinalValue(value));
          }
        });
        digitIndex += 1;
      });

      value.replaceChildren(fragment);
    });
  }

  #setFinalValue(value) {
    const amount = Number(value.dataset.slotValue);
    value.textContent = amount.toLocaleString("ko-KR", { maximumFractionDigits: 1 });
    value.dataset.slotPlayed = "1";
  }
}
