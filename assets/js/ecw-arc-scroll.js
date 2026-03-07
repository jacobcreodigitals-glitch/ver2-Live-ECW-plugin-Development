document.addEventListener("DOMContentLoaded", () => {

    gsap.registerPlugin(ScrollTrigger);
    // Select all instances of the widget
    document.querySelectorAll('.ecw-arc-cards').forEach((section) => {

        // Use GSAP context to isolate each widget instance
        const ctx = gsap.context(() => {

            const pinHeight = section.querySelector('.ecw-arc-cards__pin');
            const container = section.querySelector('.ecw-arc-cards__viewport');
            const circlesWrapper = section.querySelector('.ecw-arc-cards__track');
            const circles = section.querySelectorAll('.ecw-arc-cards__ring');

            // Defensive: exit if required elements are missing
            if (!pinHeight || !container || !circles.length) return;

            // Hide scroll indicator
            gsap.to(section.querySelector('.scroll'), {
                autoAlpha: 0,
                duration: 0.2,
                scrollTrigger: {
                    trigger: section,
                    start: 'top top',
                    end: 'top top-=1',
                    toggleActions: "play none reverse none"
                }
            });

            // Pin + Parallax
            gsap.fromTo(circlesWrapper, { y: '5%' }, { 
                y: '-5%',
                ease: 'none',
                scrollTrigger: {
                    trigger: pinHeight,
                    start: "top top",
                    endTrigger: pinHeight,
                    end: "bottom bottom",
                    pin: container,
                    scrub: true,
                    invalidateOnRefresh: true
                }
            });

            // Rotations
            const angle = 3;
            const halfRange = (circles.length - 1) * angle / 2;
            const baseRotation = -halfRange;

            circles.forEach((circle, index) => {
                const getDistPerCard = () =>
                    (pinHeight.offsetHeight - window.innerHeight) / circles.length;

                // Circle rotation
                gsap.to(circle, {
                    rotation: baseRotation + (angle * index),
                    ease: 'power1.out',
                    scrollTrigger: {
                        trigger: pinHeight,
                        start: () => "top top-=" + (getDistPerCard() * index),
                        end: () => "+=" + getDistPerCard(),
                        scrub: true,
                        invalidateOnRefresh: true
                    }
                });

                // Card rotation inside circle
                const card = circle.querySelector('.ecw-arc-cards__item');
                if (card) {
                    gsap.to(card, {
                        rotation: baseRotation + (angle * index),
                        y: '-50%',
                        ease: 'power1.out',
                        scrollTrigger: {
                            trigger: pinHeight,
                            start: () => "top top-=" + (getDistPerCard() * index),
                            end: () => "+=" + getDistPerCard(),
                            scrub: true,
                            invalidateOnRefresh: true
                        }
                    });
                }
            });

            // Safe ScrollTrigger refresh after page load
            window.addEventListener("load", () => ScrollTrigger.refresh());

            // -----------------------------
            // HANDLE RESIZE WHEN PINNED
            // -----------------------------
            let resizeTimeout;
            window.addEventListener("resize", () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    const st = ScrollTrigger.getAll().find(t => t.pin === container);
                    const sectionTop = section.offsetTop;
                    const scrollY = window.scrollY;
                    const sectionBottom = sectionTop + pinHeight.offsetHeight;

                    const isInsideOrNearBottom = scrollY >= sectionTop && scrollY <= sectionBottom;

                    if (st && isInsideOrNearBottom) {
                        window.scrollTo({ top: sectionTop, behavior: 'instant' });
                        requestAnimationFrame(() => {
                            ScrollTrigger.refresh();
                        });
                    } else {
                        ScrollTrigger.refresh();
                    }
                }, 250);
            });

        }, section); // gsap.context scoped to this section

        // Optional: cleanup if needed later
        // ctx.revert();
    });
});


//March 7 commit