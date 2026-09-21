import { fetcher } from "../core/fetcher.js";
import { attachPhoneAutoHyphen } from "../core/formatter.js";
import { API } from "../core/apiRoutes.js";
import { initCheckboxManager } from "../utils/initCheckboxManager.js";
import { resolveAdminReturnUrl } from "../core/adminNavigation.js";

import { getAdminPagesBase } from "../core/adminBase.js";

const ADMIN_PAGES_BASE = getAdminPagesBase();

export class AccountController {
  constructor(
    formSelector = "#frm",
    insertBtnSelector = "#submitBtn",
    updateBtnSelector = "#editBtn",
    deleteBtnSelector = ".delete-btn"
  ) {
    this.form = document.querySelector(formSelector);
    this.insertBtn = document.querySelector(insertBtnSelector);
    this.updateBtn = document.querySelector(updateBtnSelector);
    this.deleteButtons = document.querySelectorAll(deleteBtnSelector);
  }

  init() {
    attachPhoneAutoHyphen();

    if (this.form && this.insertBtn) {
      this.insertBtn.addEventListener("click", this.insert.bind(this));
    }

    if (this.form && this.updateBtn) {
      this.updateBtn.addEventListener("click", this.update.bind(this));
    }

    this.attachDeleteEvents();

    initCheckboxManager(async (selectedIds) => {
      const formData = new FormData();
      formData.set("mode", "delete_array");
      formData.set("ids", JSON.stringify(selectedIds));
      await this.sendRequest(formData, "선택한 계정을 삭제했습니다.");
    });
  }

  validatePasswordConfirmation(formData, isCreate = false) {
    const password = String(formData.get("upwd") || "");
    const passwordConfirm = String(formData.get("upwd_confirm") || "");

    if (isCreate && passwordConfirm.trim() === "") {
      alert("비밀번호 확인을 입력해주세요.");
      const input = document.querySelector("#upwd_confirm");
      if (input) input.focus();
      return false;
    }

    if ((isCreate || password !== "" || passwordConfirm !== "") && password !== passwordConfirm) {
      alert("비밀번호와 비밀번호 확인이 일치하지 않습니다.");
      const input = document.querySelector("#upwd_confirm");
      if (input) input.focus();
      return false;
    }

    return true;
  }

  async insert(e) {
    e.preventDefault();
    const formData = new FormData(this.form);
    formData.set("mode", "save");

    if (!this.validatePasswordConfirmation(formData, true)) {
      return;
    }

    await this.sendRequest(formData, "계정을 등록했습니다.");
  }

  async update(e) {
    e.preventDefault();
    const formData = new FormData(this.form);
    formData.set("mode", "update");

    if (!this.validatePasswordConfirmation(formData, false)) {
      return;
    }

    await this.sendRequest(formData, "계정을 수정했습니다.");
  }

  attachDeleteEvents() {
    this.deleteButtons.forEach((btn) => {
      btn.addEventListener("click", async () => {
        const id = btn.dataset.id;
        if (!id) return;
        if (!confirm("정말 삭제하시겠습니까?")) return;

        const formData = new FormData();
        formData.set("mode", "delete");
        formData.set("id", id);

        await this.sendRequest(formData, "계정을 삭제했습니다.");
      });
    });
  }

  async sendRequest(formData, successMessage) {
    try {
      const res = await fetcher(API.ACCOUNT, formData);
      alert(res.message || successMessage);

      const mode = formData.get("mode");
      if (mode === "delete" || mode === "delete_array") {
        location.reload();
      } else {
        location.href = resolveAdminReturnUrl(
          `${ADMIN_PAGES_BASE}/account`,
          this.form
        );
      }
    } catch (err) {
      alert(err.message || "처리 중 오류가 발생했습니다.");
    }
  }
}
