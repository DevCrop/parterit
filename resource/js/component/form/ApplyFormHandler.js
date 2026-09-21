export class ApplyFormHandler {
  #scrollManager;

  constructor(scrollManager) {
    this.#scrollManager = scrollManager;
  }

  init() {
    const form = document.querySelector(".no-apply-form__form");
    const modal = document.querySelector(".no-apply-result-modal");

    if (!(form instanceof HTMLFormElement) || !(modal instanceof HTMLElement)) return;
    if (form.dataset.applyInteractionBound === "1") return;
    form.dataset.applyInteractionBound = "1";

    const phoneInput = form.querySelector('input[name="phone"]');
    const submitButton = form.querySelector('[type="submit"]');
    const modalPanel = modal.querySelector(".no-apply-result-modal__panel");
    const modalTitle = modal.querySelector(".no-apply-result-modal__title");
    const modalMessage = modal.querySelector(".no-apply-result-modal__message");
    const closeButtons = modal.querySelectorAll("[data-apply-modal-close]");

    const formatPhone = (value) => {
      const digits = String(value ?? "").replace(/\D/g, "").slice(0, 11);
      if (digits.startsWith("02")) {
        if (digits.length <= 2) return digits;
        if (digits.length <= 5) return `${digits.slice(0, 2)}-${digits.slice(2)}`;
        if (digits.length <= 9) return `${digits.slice(0, 2)}-${digits.slice(2, 5)}-${digits.slice(5)}`;
        return `${digits.slice(0, 2)}-${digits.slice(2, 6)}-${digits.slice(6, 10)}`;
      }
      if (digits.length <= 3) return digits;
      if (digits.length <= 7) return `${digits.slice(0, 3)}-${digits.slice(3)}`;
      return `${digits.slice(0, 3)}-${digits.slice(3, 7)}-${digits.slice(7, 11)}`;
    };

    const setSubmitting = (submitting) => {
      if (!(submitButton instanceof HTMLButtonElement)) return;
      submitButton.disabled = submitting;
      submitButton.classList.toggle("is-loading", submitting);
    };

    let openerEl = null;
    let focusTrapHandler = null;

    const trapFocus = (event) => {
      if (event.key !== "Tab") return;
      const focusable = Array.from(modal.querySelectorAll(
        'a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex="-1"])'
      )).filter((el) => !el.hasAttribute("hidden"));
      if (!focusable.length) return;
      const first = focusable[0];
      const last = focusable[focusable.length - 1];
      if (event.shiftKey) {
        if (document.activeElement === first) { event.preventDefault(); last.focus(); }
      } else {
        if (document.activeElement === last) { event.preventDefault(); first.focus(); }
      }
    };

    const openModal = ({ success, title, message }) => {
      modal.dataset.resultStatus = success ? "success" : "error";
      modal.classList.toggle("is-success", success);
      modal.classList.toggle("is-error", !success);
      modal.setAttribute("aria-hidden", "false");
      if (modalTitle) modalTitle.textContent = title;
      if (modalMessage) modalMessage.textContent = message;

      openerEl = document.activeElement instanceof HTMLElement ? document.activeElement : null;
      this.#scrollManager.lock("apply-result-modal");

      gsap.timeline({
        defaults: { ease: "power3.out" },
        onComplete: () => {
          const firstClose = modal.querySelector("[data-apply-modal-close]");
          (firstClose ?? modalPanel)?.focus({ preventScroll: true });
        },
      })
        .to(modal, { autoAlpha: 1, duration: 0.24 })
        .to(modalPanel, { autoAlpha: 1, y: 0, scale: 1, duration: 0.42 }, "-=0.1");

      focusTrapHandler = trapFocus;
      modal.addEventListener("keydown", focusTrapHandler);
    };

    const closeModal = () => {
      gsap.timeline({
        defaults: { ease: "power2.out" },
        onComplete: () => {
          modal.setAttribute("aria-hidden", "true");
          delete modal.dataset.resultStatus;
          modal.classList.remove("is-success", "is-error");
          this.#scrollManager.unlock("apply-result-modal");
          if (focusTrapHandler) {
            modal.removeEventListener("keydown", focusTrapHandler);
            focusTrapHandler = null;
          }
          requestAnimationFrame(() => openerEl?.focus({ preventScroll: true }));
        },
      })
        .to(modalPanel, { autoAlpha: 0, y: 18, scale: 0.96, duration: 0.24 })
        .to(modal, { autoAlpha: 0, duration: 0.22 }, "-=0.12");
    };

    const getErrorMessage = (result) => {
      const errors = result?.errors && typeof result.errors === "object"
        ? Object.values(result.errors).filter(Boolean)
        : [];
      if (errors.length) return String(errors[0]);
      return result?.message || "필수 항목을 정확히 입력해 주세요.";
    };

    if (phoneInput instanceof HTMLInputElement) {
      phoneInput.addEventListener("input", () => { phoneInput.value = formatPhone(phoneInput.value); });
      phoneInput.addEventListener("blur", () => { phoneInput.value = formatPhone(phoneInput.value); });
    }

    closeButtons.forEach((button) => button.addEventListener("click", closeModal));

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape" && modal.getAttribute("aria-hidden") === "false") {
        closeModal();
      }
    });

    form.addEventListener("submit", async (event) => {
      event.preventDefault();

      if (!form.checkValidity()) {
        form.reportValidity();
        openModal({
          success: false,
          title: "입력값을 확인해 주세요",
          message: "필수 항목을 모두 입력하고 선택해 주세요.",
        });
        return;
      }

      setSubmitting(true);

      try {
        const response = await fetch(form.action, {
          method: "POST",
          headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body: new FormData(form),
        });

        const result = await response.json().catch(() => null);

        if (response.ok && result?.success) {
          form.reset();
          openModal({
            success: true,
            title: "신청이 완료되었습니다",
            message: result.message || "담당자가 확인 후 빠르게 연락드리겠습니다.",
          });
          return;
        }

        openModal({
          success: false,
          title: "입력값을 확인해 주세요",
          message: getErrorMessage(result),
        });
      } catch (_) {
        openModal({
          success: false,
          title: "신청을 완료하지 못했습니다",
          message: "잠시 후 다시 시도해 주세요.",
        });
      } finally {
        setSubmitting(false);
      }
    });
  }
}
