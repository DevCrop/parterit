/**
 * AboutTabsController
 * /about 서브네브(Company / Leadership) 탭 전환.
 * 패턴: data-* 속성 + is-active + hidden.
 *   탭   = .about-nav [data-about-tab="<key>"]
 *   패널 = [data-about-panel="<key>"]
 */
export class AboutTabsController {
  init() {
    const nav = document.querySelector(".about-nav");
    if (!nav) return;

    const tabs = Array.from(nav.querySelectorAll("[data-about-tab]"));
    const panels = Array.from(document.querySelectorAll("[data-about-panel]"));
    if (!tabs.length || !panels.length) return;

    const activate = (key) => {
      tabs.forEach((tab) => {
        const isActive = tab.dataset.aboutTab === key;
        tab.classList.toggle("is-active", isActive);
        if (isActive) tab.setAttribute("aria-current", "true");
        else tab.removeAttribute("aria-current");
      });
      panels.forEach((panel) => {
        const isActive = panel.dataset.aboutPanel === key;
        panel.classList.toggle("is-active", isActive);
        panel.hidden = !isActive;
      });
    };

    tabs.forEach((tab) => {
      tab.addEventListener("click", () => activate(tab.dataset.aboutTab));
    });

    // 좌우/상하 화살표 키 이동 (LocationTabsController 와 동일)
    tabs.forEach((tab, i) => {
      tab.addEventListener("keydown", (e) => {
        let next = i;
        if (e.key === "ArrowRight" || e.key === "ArrowDown") {
          e.preventDefault();
          next = (i + 1) % tabs.length;
        } else if (e.key === "ArrowLeft" || e.key === "ArrowUp") {
          e.preventDefault();
          next = (i - 1 + tabs.length) % tabs.length;
        } else {
          return;
        }
        tabs[next].focus();
        activate(tabs[next].dataset.aboutTab);
      });
    });
  }
}
