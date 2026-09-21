(function () {
  "use strict";

  var applying = false;
  var scheduled = false;

  function selectedBoardOption() {
    var select = document.getElementById("board_no");
    return select && select.options[select.selectedIndex]
      ? select.options[select.selectedIndex]
      : null;
  }

  function isCustomerCaseBoard() {
    var option = selectedBoardOption();
    return !!option && option.dataset.boardKind === "customer-case";
  }

  function closestBlock(selector) {
    var element = document.querySelector(selector);
    return element ? element.closest(".no-admin-block") : null;
  }

  function setBlockOrder(block, order) {
    if (!block) return;
    block.style.removeProperty("display");
    block.style.order = String(order);
    block.dataset.customerCaseVisible = "true";
  }

  function makeFeaturedRadios() {
    var field = document.querySelector('[name="extra2"]');
    if (!field || field.type === "radio") return;

    var value = String(field.value || "N").toUpperCase() === "Y" ? "Y" : "N";
    var wrapper = document.createElement("div");
    wrapper.className = "no-radio-form customer-case-featured-radio";
    wrapper.innerHTML =
      '<label for="extra2_y">' +
      '<div class="no-radio-box"><input type="radio" name="extra2" id="extra2_y" value="Y"' +
      (value === "Y" ? " checked" : "") +
      '><span><i class="bx bx-radio-circle-marked"></i></span></div>' +
      '<span class="no-radio-text">대표 사례</span></label>' +
      '<label for="extra2_n">' +
      '<div class="no-radio-box"><input type="radio" name="extra2" id="extra2_n" value="N"' +
      (value !== "Y" ? " checked" : "") +
      '><span><i class="bx bx-radio-circle-marked"></i></span></div>' +
      '<span class="no-radio-text">일반 사례</span></label>';

    field.replaceWith(wrapper);
  }

  function makeCompanyInput() {
    var field = document.querySelector('[name="extra1"]');
    if (!field || field.tagName === "INPUT") return;

    var input = document.createElement("input");
    input.type = "text";
    input.name = "extra1";
    input.id = "extra1";
    input.value = field.value || "";
    input.className = "no-input--detail";
    input.placeholder = "상호명을 입력해주세요.";
    field.replaceWith(input);
  }

  function applyCustomerCaseForm() {
    if (applying || !isCustomerCaseBoard()) return;
    applying = true;

    var editor = document.querySelector(".no-admin-column--detail");
    if (!editor) {
      applying = false;
      return;
    }

    makeCompanyInput();
    makeFeaturedRadios();

    var boardBlock = closestBlock("#board_no");
    var thumbnailBlock = closestBlock("#thumb_image");
    var titleBlock = closestBlock('[id^="title_"]');
    var companyBlock = closestBlock('[name="extra1"]');
    var categoryBlock = closestBlock("#category_no");
    var featuredBlock = closestBlock('[name="extra2"]');
    var contentsBlock = closestBlock('[id^="contents_"]');
    var allowed = [
      boardBlock,
      thumbnailBlock,
      titleBlock,
      companyBlock,
      categoryBlock,
      featuredBlock,
      contentsBlock,
    ].filter(Boolean);

    editor.classList.add("is-customer-case-form");
    editor.querySelectorAll(".no-admin-block").forEach(function (block) {
      if (allowed.indexOf(block) === -1) {
        block.style.display = "none";
        block.dataset.customerCaseHidden = "true";
      }
    });

    editor.querySelectorAll(".no-locale-panel").forEach(function (panel) {
      panel.style.display = "contents";
    });

    setBlockOrder(boardBlock, 0);
    setBlockOrder(thumbnailBlock, 10);
    setBlockOrder(titleBlock, 20);
    setBlockOrder(companyBlock, 30);
    setBlockOrder(categoryBlock, 40);
    setBlockOrder(featuredBlock, 50);
    setBlockOrder(contentsBlock, 60);

    if (categoryBlock) categoryBlock.style.display = "flex";
    var actions = editor.querySelector(".no-items-center.center");
    if (actions) actions.style.order = "100";

    applying = false;
  }

  function scheduleApply() {
    if (scheduled) return;
    scheduled = true;
    window.setTimeout(function () {
      scheduled = false;
      applyCustomerCaseForm();
    }, 0);
  }

  function plainEditorText() {
    var locale = document.getElementById("default_locale");
    var code = locale ? locale.value : "ko";
    var textarea = document.getElementById("contents_" + code);
    if (!textarea) return "";
    var html = textarea.value || "";
    if (window.jQuery && jQuery(textarea).next(".note-editor").length) {
      html = jQuery(textarea).summernote("code") || "";
    }
    var container = document.createElement("div");
    container.innerHTML = html;
    return (container.textContent || "").replace(/\u00a0/g, " ").trim();
  }

  window.validateCustomerCaseBoardForm = function () {
    if (!isCustomerCaseBoard()) return true;

    var company = document.querySelector('[name="extra1"]');
    var category = document.getElementById("category_no");
    var featured = document.querySelector('[name="extra2"]:checked');
    var thumbnail = document.getElementById("thumb_image");
    var isEdit = !!document.getElementById("no");

    if (!isEdit && thumbnail && !thumbnail.files.length) {
      alert("고객사례 썸네일을 등록해주세요.");
      thumbnail.focus();
      return false;
    }
    if (!company || !company.value.trim()) {
      alert("상호명을 입력해주세요.");
      if (company) company.focus();
      return false;
    }
    if (!category || !category.value) {
      alert("고객사례 카테고리를 선택해주세요.");
      if (category) category.focus();
      return false;
    }
    if (!featured) {
      alert("대표 사례 여부를 선택해주세요.");
      return false;
    }
    if (!plainEditorText()) {
      alert("고객사례 본문을 입력해주세요.");
      return false;
    }
    return true;
  };

  function wrapSaveFunction(name) {
    var original = window[name];
    if (typeof original !== "function" || original.__customerCaseWrapped) return;
    var wrapped = function () {
      if (!window.validateCustomerCaseBoardForm()) return;
      return original.apply(this, arguments);
    };
    wrapped.__customerCaseWrapped = true;
    window[name] = wrapped;
  }

  document.addEventListener("DOMContentLoaded", function () {
    var select = document.getElementById("board_no");
    if (select) {
      select.addEventListener("change", scheduleApply);
      if (window.jQuery) jQuery(select).on("selectmenuchange", scheduleApply);
    }

    var editor = document.querySelector(".no-admin-column--detail");
    if (editor) {
      new MutationObserver(scheduleApply).observe(editor, { childList: true, subtree: true });
    }

    wrapSaveFunction("doRegSave");
    wrapSaveFunction("doEditSave");
    scheduleApply();
  });
})();
