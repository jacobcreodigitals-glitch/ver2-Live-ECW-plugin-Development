gsap.registerPlugin(ScrollTrigger, Flip);

const initGallery = () => {
  document.querySelectorAll(".ecw-gallery-widget").forEach((gallery) => {
    if (gallery._gsapInitialized) return;
    gallery._gsapInitialized = true;

    const cols = parseInt(gallery.dataset.cols) || 3;
    const rows = parseInt(gallery.dataset.rows) || 3;
    const expandCol = parseInt(gallery.dataset.expandCol) || 0;
    const expandRow = parseInt(gallery.dataset.expandRow) || 0;

    const items = gallery.querySelectorAll(".ecw-gallery-item-widget");
    const expandIndex = expandRow * cols + expandCol;

    const inner = items[expandIndex].querySelector(".ecw-gallery-inner-item");
    const innerContent = items[expandIndex].querySelector(".ecw-gallery-inner-content-template");

    const makeCols = (col) =>
      Array.from({ length: cols }, (_, i) => (i === col ? "1fr" : "0px")).join(" ");

    const makeRows = (row) =>
      Array.from({ length: rows }, (_, i) => (i === row ? "1fr" : "0px")).join(" ");

    let ctx;

    const hardReset = () => {
      gallery.style.gridTemplateColumns = "";
      gallery.style.gridTemplateRows = "";

      items.forEach((item) => {
        gsap.set(item, { clearProps: "transform,width,height,position,top,left" });
      });

      // ✅ Zero translate before clearing — kills the stale translate3d on resize up
      gsap.set(items[expandIndex], { x: 0, y: 0, clearProps: "transform,width,height,position,top,left,zIndex" });
      gsap.set(inner, { clearProps: "minWidth,minHeight,transform,width,height" });
      gsap.set(innerContent, { clearProps: "opacity,transform" });
    };

    const createTween = () => {
      // ✅ ctx.revert() handles ScrollTrigger cleanup — keeps refresh safety
      if (ctx) {
        ctx.revert();
        ctx = null;
      }

      hardReset();

      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          ctx = gsap.context(() => {
            gallery.offsetWidth;

            gallery.style.gridTemplateColumns = makeCols(expandCol);
            gallery.style.gridTemplateRows = makeRows(expandRow);

            gallery.offsetWidth;

            const state = Flip.getState(items[expandIndex]);

            gallery.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;
            gallery.style.gridTemplateRows = `repeat(${rows}, 1fr)`;

            gallery.offsetWidth;

            gsap.set(inner, {
              minWidth: "200%",
              minHeight: "200%",
            });

            const tl = gsap.timeline({
              scrollTrigger: {
                trigger: gallery,
                start: "center center",
                end: "+=200%",
                scrub: true,
                anticipatePin: true,
                pin: gallery.parentNode,
                invalidateOnRefresh: true,
              },
            });

            tl.add(
              Flip.to(state, {
                simple: true,
                ease: "none",
                duration: 1,
              }),
              0
            );

            tl.to(
              inner,
              {
                minWidth: "100%",
                minHeight: "100%",
                ease: "none",
                duration: 0.9,
              },
              0
            );

            tl.to(
              innerContent,
              {
                opacity: 1,
                ease: "none",
                duration: 0.2,
              },
              ">"
            );

          }, gallery);

          ScrollTrigger.refresh();
        });
      });
    };

    createTween();

    let resizeTimeout;
    const resizeHandler = () => {
      clearTimeout(resizeTimeout);
      resizeTimeout = setTimeout(createTween, 250);
    };

    window.addEventListener("resize", resizeHandler);

    gallery._gsapCleanup = () => {
      window.removeEventListener("resize", resizeHandler);
      clearTimeout(resizeTimeout);
      if (ctx) ctx.revert();
    };
  });
};

document.addEventListener("DOMContentLoaded", initGallery);