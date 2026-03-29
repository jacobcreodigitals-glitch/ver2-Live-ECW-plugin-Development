document.addEventListener("DOMContentLoaded", function () {
  if (typeof window.gsap === "undefined" || typeof window.ScrollTrigger === "undefined") return;
  const { gsap, ScrollTrigger } = window;
  gsap.registerPlugin(ScrollTrigger);

  // Debounce function for resize
  const debounceTemp = (func, delay = 200) => {
    let timerTemp;
    return (...args) => {
      clearTimeout(timerTemp);
      timerTemp = setTimeout(() => func(...args), delay);
    };
  };

  // Initialize all gallery depth temp widgets separately
  gsap.utils.toArray(".ecw-gallery-depth-temp-parent").forEach((parentTemp) => {
    let timelineTemp = null; // Timeline for this instance
    const cardsTemp = parentTemp.querySelectorAll(".ecw-gallery-depth-temp-card");
    if (!cardsTemp.length) return;

    // Initialize the GSAP timeline
    const initGalleryDepthTemp = () => {
      // Prevent duplicate timeline
      if (timelineTemp) return;

      // Set initial states
      gsap.set(cardsTemp, { opacity: 0, scale: 0.6 });
      if (cardsTemp[0]) gsap.set(cardsTemp[0], { opacity: 1, scale: 1 });

      timelineTemp = gsap.timeline({
        scrollTrigger: {
          trigger: parentTemp,
          start: "top top",
          end: "+=" + cardsTemp.length * 80 + "%",
          scrub: 0.6,
          pin: true,
          anticipatePin: 1,
        }
      });

      cardsTemp.forEach((cardTemp, index) => {
        timelineTemp.to(
          cardTemp,
          { opacity: 1, scale: 1, duration: 2, ease: "none" },
          index === 0 ? 0 : "<0.5"
        );
        timelineTemp.to(
          cardTemp,
          { opacity: 0, scale: 2, duration: 2, ease: "none" }
        );
      });
    };

    // Destroy timeline
    const destroyGalleryDepthTemp = () => {
      if (timelineTemp) {
        if (timelineTemp.scrollTrigger) timelineTemp.scrollTrigger.kill();
        timelineTemp.kill();
        timelineTemp = null;
      }
      gsap.set(cardsTemp, { clearProps: "all" });
    };

    // Media query check
    const checkMediaQueryTemp = () => {
      const mqTemp = window.matchMedia("(min-width: 1024px)");
      if (mqTemp.matches) {
        initGalleryDepthTemp();
      } else {
        destroyGalleryDepthTemp();
      }
    };

    // Initial check
    checkMediaQueryTemp();

    // Listen for resize changes (debounced)
    window.addEventListener("resize", debounceTemp(checkMediaQueryTemp));
  });
});