let accordionInstanceCount = 0;

export class AccordionController {
  #root;
  #cfg;

  constructor(root, config = {}) {
    this.#root = root;
    this.#cfg = {
      itemSel:    config.itemSel    ?? '[data-acc-item]',
      triggerSel: config.triggerSel ?? '[data-acc-trigger]',
      panelSel:   config.panelSel   ?? '[data-acc-panel]',
      openClass:  config.openClass  ?? 'is-open',
      stateAttr:  config.stateAttr  ?? 'aria-expanded',
      multiOpen:  config.multiOpen  ?? false,
    };
  }

  init() {
    const items = Array.from(this.#root.querySelectorAll(this.#cfg.itemSel));
    if (!items.length) return;
    const instance = this.#root.id || `acc-${++accordionInstanceCount}`;
    items.forEach((item, i) => {
      const trigger = item.querySelector(this.#cfg.triggerSel);
      const panel   = item.querySelector(this.#cfg.panelSel);
      if (trigger && panel) {
        if (!trigger.id) trigger.id = `${instance}-trigger-${i + 1}`;
        if (!panel.id)   panel.id   = `${instance}-panel-${i + 1}`;
        trigger.setAttribute('aria-controls', panel.id);
        if (!panel.hasAttribute('role')) panel.setAttribute('role', 'region');
        panel.setAttribute('aria-labelledby', trigger.id);
      }
      if (trigger) trigger.addEventListener('click', () => this.#toggle(item, items));
    });
  }

  #toggle(target, items) {
    const isOpen = target.classList.contains(this.#cfg.openClass);
    if (!this.#cfg.multiOpen) {
      items.forEach(item => { if (item !== target) this.#setOpen(item, false); });
    }
    this.#setOpen(target, !isOpen);
  }

  #setOpen(item, open) {
    const trigger = item.querySelector(this.#cfg.triggerSel);
    const panel   = item.querySelector(this.#cfg.panelSel);
    if (!trigger || !panel) return;

    item.classList.toggle(this.#cfg.openClass, open);
    if (this.#cfg.stateAttr) trigger.setAttribute(this.#cfg.stateAttr, String(open));
    this.#animatePanel(panel, open);
  }

  #animatePanel(panel, open) {
    panel.style.overflow   = 'hidden';
    panel.style.transition = `height var(--duration-base) var(--ease-out), opacity 0.22s var(--ease-out)`;

    if (open) {
      panel.hidden        = false;
      panel.style.height  = '0px';
      panel.style.opacity = '0';

      requestAnimationFrame(() => {
        panel.style.height  = `${panel.scrollHeight}px`;
        panel.style.opacity = '1';
      });

      const onEnd = e => {
        if (e.target !== panel || e.propertyName !== 'height') return;
        panel.style.height = 'auto';
        panel.removeEventListener('transitionend', onEnd);
      };
      panel.addEventListener('transitionend', onEnd);
      return;
    }

    if (panel.hidden) return;
    panel.style.height  = `${panel.scrollHeight}px`;
    panel.style.opacity = '1';

    requestAnimationFrame(() => {
      panel.style.height  = '0px';
      panel.style.opacity = '0';
    });

    const onEnd = e => {
      if (e.target !== panel || e.propertyName !== 'height') return;
      panel.hidden = true;
      panel.removeEventListener('transitionend', onEnd);
    };
    panel.addEventListener('transitionend', onEnd);
  }
}
