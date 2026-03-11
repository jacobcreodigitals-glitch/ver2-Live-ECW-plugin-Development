//REFACTOR JS DONE

document.addEventListener("DOMContentLoaded", function () {
    // Defensive: GSAP & required plugins must exist
    if (typeof window.gsap === "undefined") return;
    if (typeof window.ScrollTrigger === "undefined" || typeof window.SplitText === "undefined") return;

    // Explicit registration (do not assume auto-registration)
    gsap.registerPlugin(ScrollTrigger, SplitText);

    /**
     * Per-widget state (no globals, auto-GC when DOM nodes go away)
     */
    const widgetState = new WeakMap();

    /**
     * Initialize a single widget instance
     */
    function initWidget(widget) {
        if (!widget) return;

        const textEl = widget.querySelector(".ecw-fill-text");
        if (!textEl) return;

        // Clean up previous instance (important for refresh / re-render)
        const prev = widgetState.get(widget);
        if (prev) {
            prev.triggers.forEach(st => st && st.kill && st.kill());
            if (prev.split && prev.split.revert) prev.split.revert();
            widgetState.delete(widget);
        }

        // Split text
        const split = new SplitText(textEl, { type: "words,chars" });
        const chars = split.chars;
        if (!chars || !chars.length) {
            split.revert();
            return;
        }

        // Read options from data attributes
        const initialColor = widget.dataset.initialColor || "#ccc";
        const finalColor   = widget.dataset.finalColor   || "#000";
        const scrub        = Number.parseFloat(widget.dataset.scrub)   || 1.5;
        const stagger      = Number.parseFloat(widget.dataset.stagger) || 0.05;

        // Initial state
        gsap.set(chars, { color: initialColor });

        // Animation
        const tween = gsap.to(chars, {
            color: finalColor,
            stagger: stagger,
            ease: "power1.inOut",
            scrollTrigger: {
                trigger: widget,
                start: "top bottom",
                end: "bottom 60%",
                scrub: scrub,
                immediateRender: false,
                invalidateOnRefresh: true
            }
        });

        // Store per-widget state
        widgetState.set(widget, {
            split,
            triggers: tween.scrollTrigger ? [tween.scrollTrigger] : []
        });
    }

    /**
     * Init all widgets on the page
     */
    const widgets = document.querySelectorAll(".ecw-fill-text-widget");
    widgets.forEach(initWidget);

    /**
     * Single, safe refresh after layout settles
     * (no permanent global listeners)
     */
    requestAnimationFrame(() => {
        if (typeof ScrollTrigger.refresh === "function") {
            ScrollTrigger.refresh();
        }
    });
});