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

    // Get the inner div for scaling
    const inner = items[expandIndex].querySelector(".ecw-gallery-inner-item");

    // Set initial scale to 2
    gsap.set(inner, { scale: 2 });

    const makeCols = (col) =>
      Array.from({ length: cols }, (_, i) => (i === col ? "1fr" : "0px")).join(" ");

    const makeRows = (row) =>
      Array.from({ length: rows }, (_, i) => (i === row ? "1fr" : "0px")).join(" ");

    let ctx;

    const createTween = () => {
      // Revert previous context if it exists
      if (ctx) ctx.revert();

      ctx = gsap.context(() => {
        // Temporarily expand gallery for Flip state
        gallery.style.gridTemplateColumns = makeCols(expandCol);
        gallery.style.gridTemplateRows = makeRows(expandRow);

        const state = Flip.getState(items[expandIndex]);

        // Reset gallery back to normal grid
        gallery.style.gridTemplateColumns = `repeat(${cols},1fr)`;
        gallery.style.gridTemplateRows = `repeat(${rows},1fr)`;

        // Create timeline tied to scroll
        const tl = gsap.timeline({
          scrollTrigger: {
            trigger: gallery,
            start: "center center",
            end: "+=100%",
            scrub: true,
            anticipatePin: true,
            pin: gallery.parentNode,
          },
        });

        // Flip animation
        tl.add(
          Flip.to(state, {
            simple: true,
            ease: "none", // linear for scrub
            duration: 1,
          }),
          0
        );

        // Scale animation (2 → 1)
        tl.to(
          inner,
          {
            scale: 1,
            ease: "none",
            duration: 0.9,
          },
          0 // starts at same time as Flip
        );

        tl.to(
          gallery,
          {
            scale: 1.1,
            ease: "none",
            duration: 0.9,
          },
          0
        );

      }, gallery);
    };

    createTween();

    // Refresh-safe resize listener
    const resizeHandler = () => createTween();
    window.addEventListener("resize", resizeHandler);

    // Cleanup on navigation/page refresh
    gallery._gsapCleanup = () => {
      window.removeEventListener("resize", resizeHandler);
      if (ctx) ctx.revert();
      ScrollTrigger.getAll().forEach((st) => st.kill());
    };
  });
};

// Run on DOM ready
document.addEventListener("DOMContentLoaded", initGallery);