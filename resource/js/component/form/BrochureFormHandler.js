export class BrochureFormHandler {
  constructor(scroll) { this.scroll = scroll; }

  init() {
    const dialog = document.querySelector('#partnerit-brochure');
    if (!dialog || typeof dialog.showModal !== 'function') return;
    const form = dialog.querySelector('form');
    const status = dialog.querySelector('[role="status"]');
    const success = dialog.querySelector('.partnerit-brochure__success');
    const submit = form.querySelector('[type="submit"]');
    let opener;
    let busy = false;
    document.querySelectorAll('[data-brochure-open]').forEach(button => {
      button.addEventListener('click', event => {
        event.preventDefault();
        if (dialog.open) return;
        opener = button;
        dialog.showModal();
        this.scroll.lock('brochure');
        dialog.querySelector(form.hidden ? '[data-brochure-download]' : '[name="company"]').focus();
      });
    });
    const close = () => { if (!busy) dialog.close(); };
    dialog.querySelectorAll('[data-brochure-close]').forEach(button => button.addEventListener('click', close));
    dialog.addEventListener('cancel', event => { if (busy) event.preventDefault(); });
    dialog.addEventListener('click', event => {
      const rect = dialog.getBoundingClientRect();
      if (event.target === dialog && (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom)) close();
    });
    dialog.addEventListener('close', () => { this.scroll.unlock('brochure'); opener?.focus({preventScroll: true}); });
    form.addEventListener('input', event => {
      event.target.removeAttribute('aria-invalid');
      const error = form.querySelector(`[data-field-error="${event.target.name}"]`);
      if (error) error.textContent = '';
    });
    form.addEventListener('submit', async event => {
      event.preventDefault();
      if (busy || !form.reportValidity()) return;
      busy = true;
      submit.disabled = true;
      form.setAttribute('aria-busy', 'true');
      status.textContent = '신청 내용을 저장하고 있습니다…';
      form.querySelectorAll('[data-field-error]').forEach(e => e.textContent = '');
      const controller = new AbortController();
      const timeout = window.setTimeout(() => controller.abort(), 20000);
      try {
        const response = await fetch(form.action, {method: 'POST', credentials: 'same-origin', signal: controller.signal, headers: {Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest'}, body: new FormData(form)});
        const result = await response.json();
        if (!response.ok || !result.success) {
          for (const [name, message] of Object.entries(result.errors || {})) {
            const field = form.elements.namedItem(name);
            field?.setAttribute('aria-invalid', 'true');
            const error = form.querySelector(`[data-field-error="${name}"]`);
            if (error) error.textContent = message;
          }
          status.textContent = result.message || '신청을 완료하지 못했습니다. 다시 시도해 주세요.';
          (form.querySelector('[aria-invalid="true"]') || status).focus();
          return;
        }
        const url = new URL(result.download_url, location.origin);
        if (url.origin !== location.origin) throw new Error('Invalid download URL');
        const link = dialog.querySelector('[data-brochure-download]');
        link.href = url.href;
        form.hidden = true;
        success.hidden = false;
        form.reset();
        link.focus();
        link.click();
      } catch (_) {
        status.textContent = '연결을 확인한 뒤 다시 시도해 주세요. 입력 내용은 유지됩니다.';
        status.focus();
      } finally {
        window.clearTimeout(timeout);
        busy = false;
        submit.disabled = false;
        form.removeAttribute('aria-busy');
      }
    });
  }
}
