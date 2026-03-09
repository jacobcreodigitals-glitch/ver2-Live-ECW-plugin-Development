document.addEventListener("DOMContentLoaded", () => {

    if (typeof window.gsap === 'undefined' || typeof window.ScrollTrigger === 'undefined') return;

    const { gsap, ScrollTrigger } = window;
    gsap.registerPlugin(ScrollTrigger);

    const BREAKPOINT = 768;
    const isMobile = () => window.innerWidth < BREAKPOINT;

    const contextMap = new Map();

    function initArcCards() {
        document.querySelectorAll('.ecw-arc-cards').forEach((section) => {
            if (contextMap.has(section)) return;

            const ctx = gsap.context(() => {

                const pinHeight = section.querySelector('.ecw-arc-cards__pin');
                const container = section.querySelector('.ecw-arc-cards__viewport');
                const circlesWrapper = section.querySelector('.ecw-arc-cards__track');
                const circles = section.querySelectorAll('.ecw-arc-cards__ring');

                if (!pinHeight || !container || !circles.length) return;

                // Hide scroll indicator safely
                const scrollIndicator = section.querySelector('.scroll');
                if (scrollIndicator) {
                    gsap.to(scrollIndicator, {
                        autoAlpha: 0,
                        duration: 0.2,
                        scrollTrigger: {
                            trigger: section,
                            start: 'top top',
                            end: 'top top-=1',
                            toggleActions: "play none reverse none"
                        }
                    });
                }

                // Pin + Parallax
                if (circlesWrapper) {
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
                }

                // Rotations
                const angle = 3;
                const halfRange = (circles.length - 1) * angle / 2;
                const baseRotation = -halfRange;

                circles.forEach((circle, index) => {
                    const getDistPerCard = () =>
                        Math.max(pinHeight.offsetHeight - window.innerHeight, 0) / circles.length;

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

            }, section);

            contextMap.set(section, ctx);
        });
    }

    function destroyArcCards() {
        contextMap.forEach((ctx) => ctx.revert());
        contextMap.clear();
        ScrollTrigger.refresh();
    }

    // Initial load
    if (!isMobile()) {
        initArcCards();
    }

    window.addEventListener('load', () => ScrollTrigger.refresh());

    // Handle resize / breakpoint crossing
    let resizeTimeout;
    let wasDesktop = !isMobile();

    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            const isDesktop = !isMobile();

            if (isDesktop && !wasDesktop) {
                initArcCards();
                wasDesktop = true;
                return;
            }

            if (!isDesktop && wasDesktop) {
                destroyArcCards();
                wasDesktop = false;
                return;
            }

            if (isDesktop) {
                ScrollTrigger.refresh();
            }
        }, 250);
    });

});