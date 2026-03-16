gsap.registerPlugin(ScrollTrigger, Flip);

const initGallery = () => {
  document.querySelectorAll(".ecw-gallery-widget").forEach((gallery) => {
    // Prevent multiple initializations
    if (gallery._gsapInitialized) return;
    gallery._gsapInitialized = true;

    const cols = parseInt(gallery.dataset.cols) || 3;
    const rows = parseInt(gallery.dataset.rows) || 3;
    const expandCol = parseInt(gallery.dataset.expandCol) || 0;
    const expandRow = parseInt(gallery.dataset.expandRow) || 0;

    const items = gallery.querySelectorAll(".ecw-gallery-item-widget");
    const expandIndex = expandRow * cols + expandCol;

    const makeCols = (col) =>
      Array.from({ length: cols }, (_, i) => (i === col ? "1fr" : "0px")).join(" ");

    const makeRows = (row) =>
      Array.from({ length: rows }, (_, i) => (i === row ? "1fr" : "0px")).join(" ");

    let ctx;

    const createTween = () => {
      // Revert previous context
      if (ctx) ctx.revert();

      ctx = gsap.context(() => {
        // Set gallery to expanded state temporarily
        gallery.style.gridTemplateColumns = makeCols(expandCol);
        gallery.style.gridTemplateRows = makeRows(expandRow);

        const state = Flip.getState(items[expandIndex]);

        // Reset to normal grid
        gallery.style.gridTemplateColumns = `repeat(${cols},1fr)`;
        gallery.style.gridTemplateRows = `repeat(${rows},1fr)`;

        const tl = gsap.timeline({
          scrollTrigger: {
            trigger: gallery,
            start: "center center",
            end: "+=100%",
            scrub: true,
            pin: gallery.parentNode,
          },
        });

        tl.add(
          Flip.to(state, {
            simple: true,
            ease: "expoScale(1,5)",
          }),
          0
        );
      }, gallery);
    };

    createTween();

    // Refresh-safe resize listener
    const resizeHandler = () => createTween();
    window.addEventListener("resize", resizeHandler);

    // Cleanup if needed on page refresh/navigation
    gallery._gsapCleanup = () => {
      window.removeEventListener("resize", resizeHandler);
      if (ctx) ctx.revert();
      ScrollTrigger.getAll().forEach((st) => st.kill());
    };
  });
};

// Run on DOM ready
document.addEventListener("DOMContentLoaded", initGallery);