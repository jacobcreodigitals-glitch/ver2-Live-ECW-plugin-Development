document.addEventListener("DOMContentLoaded", () => {

    if (typeof window.gsap === "undefined" || typeof window.ScrollTrigger === "undefined" || typeof window.Flip === "undefined") return;

    const { gsap, ScrollTrigger, Flip } = window;
    gsap.registerPlugin(ScrollTrigger, Flip);

    const MOBILE_BREAKPOINT = 1024;
    const isMobile = () => window.innerWidth <= MOBILE_BREAKPOINT;

    const contextMap = new Map();

    function initOnflipGrids() {
        document.querySelectorAll(".ecw-onflip-grid-con").forEach((grid) => {
            if (contextMap.has(grid)) return;

            const defaultColumns = grid.dataset.defaultColumns || "3";
            const newColumns     = grid.dataset.newColumns     || "1";
            const cards          = [...grid.querySelectorAll(".ecw-onflip-card")];

            if (!cards.length) return;

            if (isMobile()) {
                grid.style.gridTemplateColumns = "repeat(1, 1fr)";
                grid.classList.add("ecw-onflip-new-columns");
                return;
            }

            const ctx = gsap.context(() => {

                grid.style.gridTemplateColumns = `repeat(${defaultColumns}, 1fr)`;
                grid.classList.remove("ecw-onflip-new-columns");

                ScrollTrigger.create({
                    trigger: grid,
                    start: "top 60%",

                    onEnter: () => {
                        const state = Flip.getState(cards);
                        grid.style.gridTemplateColumns = `repeat(${newColumns}, 1fr)`;
                        grid.classList.add("ecw-onflip-new-columns");
                        Flip.from(state, { duration: 0.8, ease: "power2.inOut" });
                    },

                    onLeaveBack: () => {
                        const state = Flip.getState(cards);
                        grid.style.gridTemplateColumns = `repeat(${defaultColumns}, 1fr)`;
                        grid.classList.remove("ecw-onflip-new-columns");
                        Flip.from(state, { duration: 0.8, ease: "power2.inOut" });
                    },
                });

            }, grid);

            contextMap.set(grid, ctx);
        });
    }

    function destroyOnflipGrids() {
        contextMap.forEach((ctx) => ctx.revert());
        contextMap.clear();
        ScrollTrigger.refresh();
    }

    // Initial load
    if (!isMobile()) {
        initOnflipGrids();
    } else {
        document.querySelectorAll(".ecw-onflip-grid-con").forEach((grid) => {
            grid.style.gridTemplateColumns = "repeat(1, 1fr)";
            grid.classList.add("ecw-onflip-new-columns");
        });
    }

    window.addEventListener("load", () => ScrollTrigger.refresh());

    // Handle resize / breakpoint crossing
    let resizeTimeout;
    let wasDesktop = !isMobile();

    window.addEventListener("resize", () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            const isDesktop = !isMobile();

            if (isDesktop && !wasDesktop) {
                initOnflipGrids();
                wasDesktop = true;
                return;
            }

            if (!isDesktop && wasDesktop) {
                destroyOnflipGrids();
                document.querySelectorAll(".ecw-onflip-grid-con").forEach((grid) => {
                    grid.style.gridTemplateColumns = "repeat(1, 1fr)";
                    grid.classList.add("ecw-onflip-new-columns");
                });
                wasDesktop = false;
                return;
            }

            if (isDesktop) {
                ScrollTrigger.refresh();
            }
        }, 250);
    });

});