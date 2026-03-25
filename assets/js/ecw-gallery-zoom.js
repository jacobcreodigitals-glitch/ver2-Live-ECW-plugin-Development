gsap.registerPlugin(ScrollTrigger, Flip);

const initGallery = () => {
  const mq = window.matchMedia("(min-width: 1024px)");

  const run = () => {
    document.querySelectorAll(".ecw-gallery-widget").forEach((gallery) => {
      if (gallery._gsapInitialized) return;
      gallery._gsapInitialized = true;

      const cols      = parseInt(gallery.dataset.cols)      || 3;
      const rows      = parseInt(gallery.dataset.rows)      || 3;
      const expandCol = parseInt(gallery.dataset.expandCol) || 0;
      const expandRow = parseInt(gallery.dataset.expandRow) || 0;

      const items       = gallery.querySelectorAll(".ecw-gallery-item-widget");
      const expandIndex = expandRow * cols + expandCol;
      const expandItem  = items[expandIndex];

      const inner        = expandItem.querySelector(".ecw-gallery-inner-item");
      const innerContent = expandItem.querySelector(".ecw-gallery-inner-content-template");

      const makeCols = (col) =>
        Array.from({ length: cols }, (_, i) => (i === col ? "1fr" : "0px")).join(" ");
      const makeRows = (row) =>
        Array.from({ length: rows }, (_, i) => (i === row ? "1fr" : "0px")).join(" ");

      // All props GSAP + Flip write onto items (including Flip's individual
      // transform components and size constraints)
      const GSAP_ITEM_PROPS = [
        "transform", "translate", "rotate", "scale",
        "width", "height", "minWidth", "minHeight", "maxWidth", "maxHeight",
        "position", "top", "left", "zIndex", "willChange",
      ];

      const wipeGsapStyles = () => {
        items.forEach((item) => {
          GSAP_ITEM_PROPS.forEach((prop) => (item.style[prop] = ""));
        });
        gallery.style.gridTemplateColumns = "";
        gallery.style.gridTemplateRows    = "";
      };

      // Remove the pin spacer ScrollTrigger inserts next to gallery.parentNode
      const removePinSpacer = () => {
        const pinSpacer = gallery.parentNode.parentNode?.querySelector(
          ".pin-spacer"
        );
        if (pinSpacer && pinSpacer.contains(gallery.parentNode)) {
          // Unwrap: move parentNode out of the pin spacer, then remove spacer
          pinSpacer.replaceWith(gallery.parentNode);
        }
      };

      let ctx;
      let scrollTriggerInstance;

      const createTween = () => {
        if (!mq.matches) return;

        // 1. Jump to end → element at natural position, transform: none
        if (scrollTriggerInstance) {
          scrollTriggerInstance.progress(1);
          scrollTriggerInstance.kill();
          scrollTriggerInstance = null;
        }

        // 2. Revert context
        if (ctx) {
          ctx.revert();
          ctx = null;
        }

        // 3. Remove pin spacer so parentNode is back in normal flow
        removePinSpacer();

        // 4. Wipe all GSAP/Flip-written props from items
        wipeGsapStyles();

        // 5. Two rAFs — let browser fully repaint clean layout
        requestAnimationFrame(() => {
          requestAnimationFrame(() => {
            ctx = gsap.context(() => {

              void gallery.offsetWidth;

              // Collapse → FROM rect
              gallery.style.gridTemplateColumns = makeCols(expandCol);
              gallery.style.gridTemplateRows    = makeRows(expandRow);
              void gallery.offsetWidth;

              const state = Flip.getState(expandItem);

              // Restore → TO rect
              gallery.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;
              gallery.style.gridTemplateRows    = `repeat(${rows}, 1fr)`;
              void gallery.offsetWidth;

              gsap.set(inner, { minWidth: "200%", minHeight: "200%" });

              const tl = gsap.timeline({
                scrollTrigger: {
                  trigger: gallery,
                  start: "center center",
                  end: "+=200%",
                  scrub: true,
                  anticipatePin: true,
                  pin: gallery.parentNode,
                  invalidateOnRefresh: true,
                  onInit(self) {
                    scrollTriggerInstance = self;
                  },
                },
              });

              tl.add(
                Flip.to(state, { simple: true, ease: "none", duration: 1 }),
                0
              );

              tl.to(inner, {
                minWidth: "100%", minHeight: "100%",
                ease: "none", duration: 0.9,
              }, 0);

              tl.to(innerContent, {
                opacity: 1, ease: "none", duration: 0.2,
              }, ">");

            }, gallery);

            ScrollTrigger.refresh();
          });
        });
      };

      createTween();

      let resizeTimeout;
      const resizeHandler = () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
          if (mq.matches) {
            createTween();
          } else {
            if (scrollTriggerInstance) {
              scrollTriggerInstance.progress(1);
              scrollTriggerInstance.kill();
              scrollTriggerInstance = null;
            }
            if (ctx) { ctx.revert(); ctx = null; }
            removePinSpacer();
            wipeGsapStyles();
          }
        }, 250);
      };

      window.addEventListener("resize", resizeHandler);

      gallery._gsapCleanup = () => {
        window.removeEventListener("resize", resizeHandler);
        clearTimeout(resizeTimeout);
        if (scrollTriggerInstance) {
          scrollTriggerInstance.progress(1);
          scrollTriggerInstance.kill();
          scrollTriggerInstance = null;
        }
        if (ctx) { ctx.revert(); ctx = null; }
        removePinSpacer();
        wipeGsapStyles();
        gallery._gsapInitialized = false;
      };
    });
  };

  if (mq.matches) run();

  mq.addEventListener("change", (e) => {
    if (e.matches) {
      document.querySelectorAll(".ecw-gallery-widget").forEach((g) => {
        g._gsapInitialized = false;
      });
      run();
    } else {
      document.querySelectorAll(".ecw-gallery-widget").forEach((g) => {
        if (g._gsapCleanup) g._gsapCleanup();
      });
    }
  });
};

document.addEventListener("DOMContentLoaded", initGallery);