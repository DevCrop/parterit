import { ScrollManager } from "./core/ScrollManager.js";
import { HeaderManager } from "./ui/header/HeaderManager.js";
import { MarqueeController } from "./animation/MarqueeController.js";
import { RevealAnimator } from "./animation/RevealAnimator.js";
import { FloatingButtonController } from "./ui/FloatingButtonController.js";
import { ApplyFormHandler } from "./component/form/ApplyFormHandler.js";
import { SwiperController } from "./ui/SwiperController.js";
import { AboutTabsController } from "./ui/AboutTabsController.js";
import { BusinessNavController } from "./ui/BusinessNavController.js";
import { PopupController } from "./ui/PopupController.js";
import { LazyVideoController } from "./component/main/LazyVideoController.js";
import { SlotCounter } from "./animation/SlotCounter.js";
import { HeroMotionController } from "./animation/HeroMotionController.js";
import { CompanyVisionController } from "./animation/CompanyVisionController.js";
import { ImpactChartController } from "./animation/ImpactChartController.js";
import { NarrativeAos } from "./animation/NarrativeAos.js";

$(document).ready(function () {
  gsap.registerPlugin(ScrollTrigger);

  const scroll = new ScrollManager();
  scroll.init();

  if (window.AOS && typeof window.AOS.init === "function") {
    new NarrativeAos().prepare();
    window.AOS.init({
      duration: 800,
      easing: "ease-out-quart",
      once: true,
      offset: 96,
      anchorPlacement: "top-bottom",
      disable: window.matchMedia("(prefers-reduced-motion: reduce)").matches,
    });
    window.requestAnimationFrame(() => window.AOS.refreshHard());
  }

  new HeaderManager(scroll).init();
  new MarqueeController().init();
  new RevealAnimator().init();
  new FloatingButtonController().init();
  new ApplyFormHandler(scroll).init();
  new SwiperController().init();
  new AboutTabsController().init();
  new BusinessNavController().init();
  new PopupController().init();
  new LazyVideoController().init();
  new SlotCounter().init();
  new HeroMotionController().init();
  new CompanyVisionController().init();
  new ImpactChartController().init();
});
