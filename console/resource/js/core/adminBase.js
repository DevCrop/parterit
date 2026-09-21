/**
 * 관리자 URL 베이스. PHP(inc/admin.js.php)가 주입한 전역을 우선하고,
 * 없으면 이 모듈 위치(…/resource/js/core) 기준으로 관리자 루트를 계산합니다.
 */
export function getAdminBase() {
  if (globalThis.NO_ADMIN_BASE) {
    return String(globalThis.NO_ADMIN_BASE).replace(/\/$/, "") || "";
  }
  try {
    return new URL("../../..", import.meta.url).pathname.replace(/\/$/, "") || "";
  } catch {
    return "";
  }
}

export function getAdminPagesBase() {
  if (globalThis.NO_ADMIN_PAGES_BASE) {
    return String(globalThis.NO_ADMIN_PAGES_BASE).replace(/\/$/, "") || "";
  }
  const root = getAdminBase();

  return root ? `${root}/pages` : "";
}
