import { getAdminBase } from "./adminBase.js";

const ADMIN_BASE = getAdminBase();

export const API = {
  ACCOUNT: `${ADMIN_BASE}/Controller/AccountController.php`,
  BOARD: `${ADMIN_BASE}/Controller/BoardController.php`,
  SETTING: `${ADMIN_BASE}/Controller/SettingController.php`,
  SEO: `${ADMIN_BASE}/Controller/SeoController.php`,
  BANNER: `${ADMIN_BASE}/Controller/BannerController.php`,
  POPUP: `${ADMIN_BASE}/Controller/PopupController.php`,
};
