document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.gsap === 'undefined' || typeof window.ScrollTrigger === 'undefined') return;

    const { gsap, ScrollTrigger } = window;
    gsap.registerPlugin(ScrollTrigger);

    const BREAKPOINT = 768;
    const isMobile = () => window.innerWidth < BREAKPOINT;

    // Store contexts per parent for cleanup
    const contextMap = new Map();

    function initSliders() {
        document.querySelectorAll('.ecw-hr-slider-parent').forEach((parent) => {
            // Skip if already initialized
            if (contextMap.has(parent)) return;

            const ctx = gsap.context(() => {

                const container = parent.querySelector('.ecw-hr-slider-content');
                if (!container) return;

                const slides = container.querySelectorAll('.ecw-hr-content-slide');
                if (!slides.length) return;

                const additionalOffset = parseInt(parent.dataset.endOffset || 0);

                // --- TRIGGER ELEMENT LOGIC ---
                let customClass = parent.dataset.scrollTrigger || '';
                let triggerElement = parent;

                if (customClass) {
                    if (!customClass.startsWith('.')) customClass = '.' + customClass;
                    const el = document.querySelector(customClass);
                    if (el) triggerElement = el;
                }

                // --- APPLY STYLES TO THE TRIGGER ELEMENT ---
                triggerElement.style.position = 'relative';
                triggerElement.style.transition = 'none';
                triggerElement.style.overflow = triggerElement.dataset.overflow || 'hidden';

                const getScrollDistance = () =>
                    Math.max(0, container.scrollWidth - document.documentElement.clientWidth);

                if (getScrollDistance() <= 0) return;

                gsap.to(container, {
                    x: () => -(getScrollDistance() + additionalOffset),
                    ease: 'none',
                    scrollTrigger: {
                        trigger: triggerElement,
                        start: 'top top',
                        end: () => '+=' + (getScrollDistance() + additionalOffset),
                        scrub: true,
                        pin: true,
                        anticipatePin: 1,
                        invalidateOnRefresh: true,
                        refreshPriority: 0
                    }
                });

            }, parent);

            contextMap.set(parent, ctx);
        });
    }

    function destroySliders() {
        contextMap.forEach((ctx, parent) => {
            ctx.revert();

            // Clean up inline styles left by GSAP/ScrollTrigger on trigger element
            let customClass = parent.dataset.scrollTrigger || '';
            let triggerElement = parent;
            if (customClass) {
                if (!customClass.startsWith('.')) customClass = '.' + customClass;
                const el = document.querySelector(customClass);
                if (el) triggerElement = el;
            }

            triggerElement.style.position = '';
            triggerElement.style.transition = '';
            triggerElement.style.overflow = '';

            // Reset container transform
            const container = parent.querySelector('.ecw-hr-slider-content');
            if (container) {
                gsap.set(container, { clearProps: 'x,transform' });
            }
        });

        contextMap.clear();
        ScrollTrigger.refresh();
    }

    // Initial load
    if (!isMobile()) {
        initSliders();
    }

    // Safe ScrollTrigger refresh after full page load
    window.addEventListener('load', () => ScrollTrigger.refresh());

    // -----------------------------
    // HANDLE RESIZE WHEN PINNED
    // -----------------------------
    let resizeTimeout;
    let wasDesktop = !isMobile();

    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            const isDesktop = !isMobile();

            // --- Breakpoint crossing: init or destroy ---
            if (isDesktop && !wasDesktop) {
                // Crossed from mobile → desktop
                initSliders();
                wasDesktop = true;
                return;
            }

            if (!isDesktop && wasDesktop) {
                // Crossed from desktop → mobile
                destroySliders();
                wasDesktop = false;
                return;
            }

            // --- Same breakpoint zone: handle resize while pinned (desktop only) ---
            if (!isDesktop) return;

            document.querySelectorAll('.ecw-hr-slider-parent').forEach((parent) => {
                let triggerElement = parent;
                let customClass = parent.dataset.scrollTrigger || '';
                if (customClass) {
                    if (!customClass.startsWith('.')) customClass = '.' + customClass;
                    const el = document.querySelector(customClass);
                    if (el) triggerElement = el;
                }

                const st = ScrollTrigger.getAll().find(t => t.pin === triggerElement);
                const sectionTop = triggerElement.offsetTop;
                const scrollY = window.scrollY;
                const container = parent.querySelector('.ecw-hr-slider-content');
                const sectionBottom = sectionTop + (container
                    ? Math.max(0, container.scrollWidth - document.documentElement.clientWidth)
                    : 0);

                const isInsideOrNearBottom = scrollY >= sectionTop && scrollY <= sectionBottom;

                if (st && isInsideOrNearBottom) {
                    window.scrollTo({ top: sectionTop, behavior: 'instant' });
                    requestAnimationFrame(() => ScrollTrigger.refresh());
                } else {
                    ScrollTrigger.refresh();
                }
            });
        }, 250);
    });
});


//March 7 commit