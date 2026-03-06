document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.gsap === 'undefined' || typeof window.ScrollTrigger === 'undefined') return;

    const { gsap, ScrollTrigger } = window;
    gsap.registerPlugin(ScrollTrigger);

    document.querySelectorAll('.ecw-hr-slider-parent').forEach((parent) => {

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

        }, parent); // gsap.context scoped to this parent

    });

    // Safe ScrollTrigger refresh after full page load
    window.addEventListener('load', () => ScrollTrigger.refresh());

    // -----------------------------
    // HANDLE RESIZE WHEN PINNED
    // -----------------------------
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
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