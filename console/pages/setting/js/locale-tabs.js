/* 다국어 입력용 locale 탭 (seo.config / seo.new / seo.edit / board.add / board.view 공용)
 *
 * 마크업 규칙:
 *   .no-locale-tabs > button.no-locale-tab[aria-controls="<panel-id>"][data-locale="<lc>"]
 *   fieldset.no-locale-panel[id="<panel-id>"]
 *
 * 한 페이지에 여러 탭 그룹이 있을 수 있으므로, 각 .no-locale-tabs 컨테이너 단위로 바인딩.
 */
(function () {
  function bindGroup(tabsContainer) {
    var tabs = tabsContainer.querySelectorAll(".no-locale-tab");
    if (!tabs.length) return;

    var panels = [];
    tabs.forEach(function (tab) {
      var panelId = tab.getAttribute("aria-controls");
      if (!panelId) return;
      var panel = document.getElementById(panelId);
      if (panel) panels.push(panel);
    });

    tabs.forEach(function (tab) {
      tab.addEventListener("click", function () {
        var locale = tab.dataset.locale;
        var activePanelId = tab.getAttribute("aria-controls");

        tabs.forEach(function (t) {
          var on = t.dataset.locale === locale;
          t.classList.toggle("is-active", on);
          t.setAttribute("aria-selected", on ? "true" : "false");
        });

        panels.forEach(function (panel) {
          var on = panel.id === activePanelId;
          panel.classList.toggle("is-active", on);
          if (on) {
            panel.removeAttribute("hidden");
          } else {
            panel.setAttribute("hidden", "");
          }
        });
      });
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".no-locale-tabs").forEach(bindGroup);
  });
})();
