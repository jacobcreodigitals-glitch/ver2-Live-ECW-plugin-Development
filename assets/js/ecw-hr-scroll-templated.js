document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.gsap === 'undefined' || typeof window.ScrollTrigger === 'undefined') return;

    const { gsap, ScrollTrigger } = window;
    gsap.registerPlugin(ScrollTrigger);

    const instanceState = new WeakMap();
    const instances = document.querySelectorAll('.ecw-hr-slider-templated-parent');
    if (!instances.length) return;

    instances.forEach((parent) => {

        if (instanceState.has(parent)) {
            const prev = instanceState.get(parent);
            if (prev?.context) prev.context.revert();
        }

        const container = parent.querySelector('.ecw-hr-slider-templated-content');
        if (!container) return;

        const slides = container.querySelectorAll('.ecw-hr-content-templated-slide');
        if (!slides.length) return;

        const additionalOffset = parseInt(parent.dataset.endOffset || 0);


        // --- CLASS-ONLY TRIGGER LOGIC ---
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

        const ctx = gsap.context(() => {
            const getScrollDistance = () => Math.max(0, container.scrollWidth - document.documentElement.clientWidth);
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
                    markers: true,
                    anticipatePin: 1,
                    invalidateOnRefresh: true,
                    refreshPriority: 0
                }
            });

        }, parent);

        instanceState.set(parent, { context: ctx });
    });

    requestAnimationFrame(() => ScrollTrigger.refresh());
});