document.addEventListener("DOMContentLoaded", function () {
  if (typeof window.gsap === "undefined" || typeof window.ScrollTrigger === "undefined") return;
  const { gsap, ScrollTrigger } = window;
  gsap.registerPlugin(ScrollTrigger);

  // Debounce function for resize
  const debounce = (func, delay = 200) => {
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => func(...args), delay);
    };
  };

  // Initialize all gallery depth widgets separately
  gsap.utils.toArray(".ecw-gallery-depth-parent").forEach((parent) => {
    let timeline = null; // Timeline for this instance
    const cards = parent.querySelectorAll(".ecw-gallery-depth-card");
    if (!cards.length) return;

    // Initialize the GSAP timeline
    const initGalleryDepth = () => {
      // Prevent duplicate timeline
      if (timeline) return;

      // Set initial states
      gsap.set(cards, { opacity: 0, scale: 0.6 });
      if (cards[0]) gsap.set(cards[0], { opacity: 1, scale: 1 });

      timeline = gsap.timeline({
        scrollTrigger: {
          trigger: parent,
          start: "top top",
          end: "+=" + cards.length * 80 + "%",
          scrub: 0.6,
          pin: true,
          anticipatePin: 1,
        }
      });

      cards.forEach((card, index) => {
        timeline.to(
          card,
          { opacity: 1, scale: 1, duration: 2, ease: "none" },
          index === 0 ? 0 : "<0.5"
        );
        timeline.to(
          card,
          { opacity: 0, scale: 2, duration: 2, ease: "none" }
        );
      });
    };

    // Destroy timeline
    const destroyGalleryDepth = () => {
      if (timeline) {
        if (timeline.scrollTrigger) timeline.scrollTrigger.kill();
        timeline.kill();
        timeline = null;
      }
      gsap.set(cards, { clearProps: "all" });
    };

    // Media query check
    const checkMediaQuery = () => {
      const mq = window.matchMedia("(min-width: 1024px)");
      if (mq.matches) {
        initGalleryDepth();
      } else {
        destroyGalleryDepth();
      }
    };

    // Initial check
    checkMediaQuery();

    // Listen for resize changes (debounced)
    window.addEventListener("resize", debounce(checkMediaQuery));
  });
});