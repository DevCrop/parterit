/**
 * Defers `<video data-lazy-video>` source loading until the element is near the
 * viewport. Each source carries its URL in `data-src`; on intersection we promote
 * `data-src` → `src`, run `video.load()`, then attempt autoplay.
 *
 * Reasoning: keeping `<source src>` empty at first paint guarantees zero bytes
 * for off-screen videos even when `preload="metadata"` is the browser default.
 * IntersectionObserver fires ~200px before entry so playback starts on time.
 */
export class LazyVideoController {
  #observer = null;

  init() {
    const videos = document.querySelectorAll("video[data-lazy-video]");
    if (!videos.length) return;

    if (!("IntersectionObserver" in window)) {
      videos.forEach((v) => this.#load(v));
      return;
    }

    this.#observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          this.#load(entry.target);
          this.#observer.unobserve(entry.target);
        });
      },
      { rootMargin: "200px 0px" }
    );

    videos.forEach((v) => this.#observer.observe(v));
  }

  #load(video) {
    if (video.dataset.lazyVideoLoaded === "1") return;
    video.dataset.lazyVideoLoaded = "1";

    video.querySelectorAll("source[data-src]").forEach((source) => {
      source.src = source.dataset.src;
      delete source.dataset.src;
    });

    video.load();

    const promise = video.play();
    if (promise && typeof promise.catch === "function") {
      // Autoplay can be blocked even when muted on some browsers — silent.
      promise.catch(() => {});
    }
  }
}
