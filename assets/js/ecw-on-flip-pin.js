document.addEventListener("DOMContentLoaded", function () {

  if (!window.gsap || !window.ScrollTrigger || !window.Flip) return;

  gsap.registerPlugin(Flip, ScrollTrigger);

  document.querySelectorAll(".ecw-parent-onflip-pin-widget").forEach((section, i) => {

    const gridCon      = section.querySelector(".ecw-grid-onflip-pin-con");
    const containerCon = section.querySelector(".ecw-container-onflip-pin-con");
    const leftList     = section.querySelector(".ecw-onflip-pin-left-list");
    const rightList    = section.querySelector(".ecw-onflip-pin-right-list");
    const cards        = [...gridCon.querySelectorAll(".ecw-onflip-pin-card")];

    if (!gridCon || !containerCon || !leftList || !rightList || !cards.length) return;

    let currentIndex = -1;
    let isListLayout = false;

    const ctx = gsap.context(() => {

      function moveCard(index) {

        if (index === currentIndex) return;

        const state = Flip.getState(cards);
        const cardInRight = rightList.querySelector(".ecw-onflip-pin-card");

        currentIndex = index;

        if (cardInRight) {

          const originalIndex = cards.indexOf(cardInRight);
          const leftCards = [...leftList.children];

          if (originalIndex >= leftCards.length) {
            leftList.appendChild(cardInRight);
          } else {
            leftList.insertBefore(cardInRight, leftCards[originalIndex]);
          }

        }

        if (index >= 0 && index < cards.length) {
          rightList.appendChild(cards[index]);
        }

        Flip.from(state, {
          duration: 0.8,
          ease: "power2.inOut",
          absolute: true
        });

      }

      function goToList() {

        const state = Flip.getState(cards);

        gridCon.style.display = "none";
        containerCon.style.display = "flex";

        cards.forEach(card => leftList.appendChild(card));

        currentIndex = -1;
        isListLayout = true;

        Flip.from(state, {
          duration: 1,
          ease: "power2.inOut",
          absolute: true,
          stagger: 0.08
        });

      }

      function goToGrid() {

        const state = Flip.getState(cards);

        containerCon.style.display = "none";
        gridCon.style.display = "grid";

        cards.forEach(card => gridCon.appendChild(card));

        currentIndex = -1;
        isListLayout = false;

        Flip.from(state, {
          duration: 1,
          ease: "power2.inOut",
          absolute: true,
          stagger: 0.08
        });

      }

      const triggerEnter = ScrollTrigger.create({
        trigger: section,
        start: "top center",
        onEnter: goToList,
        onLeaveBack: goToGrid
      });

      const triggerScroll = ScrollTrigger.create({
        trigger: section,
        start: "top top",
        end: () => `+=${cards.length * 600}`,
        pin: true,
        pinSpacing: true,
        scrub: 1,
        invalidateOnRefresh: true,

        onUpdate(self) {

          if (!isListLayout) return;

          const rawIndex = Math.round(self.progress * cards.length) - 1;
          const newIndex = Math.max(-1, Math.min(rawIndex, cards.length - 1));

          if (newIndex !== currentIndex) {
            moveCard(newIndex);
          }

        }
      });

      // Resize Safety
      ScrollTrigger.addEventListener("refreshInit", () => {
        currentIndex = -1;
      });

      // Store triggers for cleanup
      section._ecwTriggers = [triggerEnter, triggerScroll];

    }, section);

  });

  // Global refresh safety
  window.addEventListener("load", () => {
    ScrollTrigger.refresh();
  });

  // Resize safety
  let resizeTimer;
  window.addEventListener("resize", () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
      ScrollTrigger.refresh();
    }, 200);
  });

});