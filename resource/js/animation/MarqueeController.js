export class MarqueeController {
  init() {
    this.initPartneritPartners();
    const marquees = document.querySelectorAll("[data-marquee]");
    if (!marquees.length) return;

    const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    marquees.forEach((el) => {
      if (el.dataset.marqueeInitialized === "1") return;
      el.dataset.marqueeInitialized = "1";

      const track = el.querySelector(".no-marquee__track");
      if (!track) return;

      // 1) 원본 ul을 복제해 트랙 너비를 확보한다 (motion 여부 무관)
      //    PHP는 ul 1개만 출력 → JS가 1회 복제하여 seamless loop에 필요한 2배 너비를 만든다.
      const sourceList = track.querySelector(".no-partners-marquee__list, .no-marquee__list");
      if (sourceList) {
        const clone = sourceList.cloneNode(true);
        clone.setAttribute("aria-hidden", "true");
        clone.removeAttribute("aria-label");
        track.appendChild(clone);
      }

      // 2) reduced-motion이면 GSAP 스킵 (정적 32개 노출)
      if (reducedMotion) return;

      const speed = parseFloat(el.dataset.speed) || 28;
      const isRight = el.dataset.direction === "right";

      if (isRight) {
        gsap.fromTo(track, { xPercent: -50 }, {
          xPercent: 0,
          ease: "none",
          duration: speed,
          repeat: -1,
        });
      } else {
        gsap.to(track, {
          xPercent: -50,
          ease: "none",
          duration: speed,
          repeat: -1,
        });
      }
    });
  }
  initPartneritPartners() {
    document.querySelectorAll(".partnerit-partners__marquee").forEach((marquee) => {
      if (marquee.dataset.marqueeInitialized === "1") return;
      const track = marquee.querySelector(".partnerit-partners__track");
      const list = track?.querySelector(".partnerit-partners__list");
      if (!list || !list.children.length) return;
      marquee.dataset.marqueeInitialized = "1";
      const originals = Array.from(list.children, (item) => item.cloneNode(true));
      let lastWidth = 0;
      const fill = () => {
        const width = marquee.clientWidth;
        if (!width || width === lastWidth) return;
        lastWidth = width;
        list.replaceChildren(...originals.map((item) => item.cloneNode(true)));
        // 각 반복 그룹이 뷰포트보다 길어야 루프 끝에서도 공백이 없습니다.
        while (list.getBoundingClientRect().width < width) {
          list.append(...originals.map((item) => {
            const clone = item.cloneNode(true);
            clone.setAttribute("aria-hidden", "true");
            return clone;
          }));
        }
        Array.from(track.children).slice(1).forEach((item) => item.remove());
        const duplicate = list.cloneNode(true);
        duplicate.setAttribute("aria-hidden", "true");
        track.append(duplicate);
      };
      fill();
      new ResizeObserver(fill).observe(marquee);
    });
  }
}
