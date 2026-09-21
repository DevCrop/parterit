export async function adminConfirm(message) {
  if (window.Modal && typeof window.Modal.confirm === "function") {
    return Boolean(await window.Modal.confirm(message));
  }

  const confirmDialog = window["confirm"];
  return typeof confirmDialog === "function" ? Boolean(confirmDialog(message)) : true;
}
