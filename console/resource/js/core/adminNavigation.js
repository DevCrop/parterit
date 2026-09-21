const RETURN_PARAM = "return_url";

function resolveInternalUrl(value, base = window.location.href) {
  if (!value) return null;

  try {
    const url = new URL(value, base);
    if (url.origin !== window.location.origin) return null;
    return url;
  } catch (_) {
    return null;
  }
}

function normalizeInternalUrl(value) {
  if (!value) return "";

  const url = resolveInternalUrl(value);
  return url ? `${url.pathname}${url.search}${url.hash}` : "";
}

function currentRelativeUrl() {
  return `${window.location.pathname}${window.location.search}${window.location.hash}`;
}

function isDecoratableHref(href) {
  if (!href) return false;
  if (
    href.startsWith("#") ||
    href.startsWith("javascript:") ||
    href.startsWith("mailto:") ||
    href.startsWith("tel:")
  ) {
    return false;
  }

  const url = resolveInternalUrl(href);
  if (!url) return false;
  const pathname = url.pathname.toLowerCase();
  const filename = pathname.split("/").pop() || "";

  return (
    /(view|edit|new)\.php$/.test(filename) ||
    filename === "board.role.php" ||
    filename === "account" ||
    pathname.endsWith("/account")
  );
}

function isBackLinkHref(href) {
  if (!href) return false;

  const url = resolveInternalUrl(href);
  if (!url) return false;
  const pathname = url.pathname.toLowerCase();
  const filename = pathname.split("/").pop() || "";

  return (
    filename === "index.php" ||
    filename.endsWith(".list.php") ||
    filename === "seo.php" ||
    filename === "external.tag.php" ||
    filename === "board.role.php" ||
    filename === "account" ||
    pathname.endsWith("/account")
  );
}

export function appendReturnUrlParam(href, returnUrl = currentRelativeUrl()) {
  const normalizedReturnUrl = normalizeInternalUrl(returnUrl);
  if (!normalizedReturnUrl) return href;

  const url = resolveInternalUrl(href);
  if (!url) return href;
  url.searchParams.set(RETURN_PARAM, normalizedReturnUrl);
  return `${url.pathname}${url.search}${url.hash}`;
}

export function ensureReturnUrlInput(form = document.querySelector("#frm")) {
  if (!form) return null;

  const params = new URLSearchParams(window.location.search);
  const returnUrl = normalizeInternalUrl(params.get(RETURN_PARAM) || "");
  if (!returnUrl) return null;

  let input = form.querySelector(`input[name="${RETURN_PARAM}"]`);
  if (!input) {
    input = document.createElement("input");
    input.type = "hidden";
    input.name = RETURN_PARAM;
    form.appendChild(input);
  }

  input.value = returnUrl;
  return input;
}

export function resolveAdminReturnUrl(defaultUrl, form = document.querySelector("#frm")) {
  const formValue = normalizeInternalUrl(
    form?.querySelector(`input[name="${RETURN_PARAM}"]`)?.value || ""
  );
  if (formValue) return formValue;

  const searchValue = normalizeInternalUrl(
    new URLSearchParams(window.location.search).get(RETURN_PARAM) || ""
  );
  if (searchValue) return searchValue;

  return normalizeInternalUrl(defaultUrl) || defaultUrl;
}

function decorateListLinks() {
  const isListLikePage =
    document.querySelector(".no-pagination") ||
    document.querySelector(".no-table") ||
    document.querySelector(".no-search");

  if (!isListLikePage) return;

  const returnUrl = currentRelativeUrl();
  document.querySelectorAll("a[href]").forEach((anchor) => {
    const href = anchor.getAttribute("href");
    if (!isDecoratableHref(href)) return;
    anchor.setAttribute("href", appendReturnUrlParam(href, returnUrl));
  });
}

function decorateBackLinks() {
  const returnUrl = resolveAdminReturnUrl("");
  if (!returnUrl) return;

  document.querySelectorAll("a[href]").forEach((anchor) => {
    const href = anchor.getAttribute("href");
    if (!isBackLinkHref(href)) return;
    anchor.setAttribute("href", returnUrl);
  });
}

export function initAdminNavigationState() {
  ensureReturnUrlInput();
  decorateListLinks();
  decorateBackLinks();

  window.noAdminResolveReturnUrl = (defaultUrl, form = document.querySelector("#frm")) =>
    resolveAdminReturnUrl(defaultUrl, form);
  window.noAdminAppendReturnUrlParam = (href, returnUrl) =>
    appendReturnUrlParam(href, returnUrl);
}
