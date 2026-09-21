import { AccountController } from "./Controller/AccountController.js";
import { SettingController } from "./Controller/SettingController.js";
import { SeoController } from "./Controller/SeoController.js";
import { BannerController } from "./Controller/BannerController.js";
import { PopupController } from "./Controller/PopupController.js";
import { attachRadioToggle } from "./utils/initRadioToggle.js";
import { initAdminNavigationState } from "./core/adminNavigation.js";

document.addEventListener("DOMContentLoaded", () => {
  initAdminNavigationState();
  const page = document.body.dataset.page;

  switch (page) {
    case "account":
      const accountController = new AccountController();
      accountController.init();
      break;

    case "setting":
      const settingController = new SettingController();
      settingController.init();
      break;

    case "seo":
      const seoController = new SeoController();
      seoController.init();
      break;

    case "banner":
      const bannerController = new BannerController();
      bannerController.init();
      break;

    case "popup":
      const popupController = new PopupController();
      popupController.init();
      break;

    default:
      break;
  }
});
