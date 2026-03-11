document.addEventListener("DOMContentLoaded", () => {

gsap.registerPlugin(Flip, ScrollTrigger);

document.querySelectorAll(".ecw-onflip-grid-con").forEach((grid) => {

    const cards = [...grid.querySelectorAll(".ecw-onflip-card")];

    const defaultColumns = grid.dataset.defaultColumns;
    const newColumns = grid.dataset.newColumns;

    grid.style.gridTemplateColumns = `repeat(${defaultColumns}, 1fr)`;

    ScrollTrigger.create({
        trigger: grid,
        // markers:true,
        start: "top 60%",
        onEnter: () => {

            const state = Flip.getState(cards);

            grid.style.gridTemplateColumns = `repeat(${newColumns}, 1fr)`;
            grid.classList.add("ecw-onflip-new-columns");

            Flip.from(state, {
                duration: 0.8,
                ease: "power2.inOut"
            });

        },

        onLeaveBack: () => {

            const state = Flip.getState(cards);

            grid.style.gridTemplateColumns = `repeat(${defaultColumns}, 1fr)`;
            grid.classList.remove("ecw-onflip-new-columns");

            Flip.from(state, {
                duration: 0.8,
                ease: "power2.inOut"
            });

        }

    });

});

});