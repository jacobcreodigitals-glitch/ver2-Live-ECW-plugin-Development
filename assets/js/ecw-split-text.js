document.addEventListener("DOMContentLoaded", function () {

    // Defensive: GSAP & required plugins must exist
    if (
        typeof window.gsap === "undefined" ||
        typeof window.ScrollTrigger === "undefined" ||
        typeof window.SplitText === "undefined"
    ) {
        return;
    }

    // Explicit plugin registration
    gsap.registerPlugin(ScrollTrigger, SplitText);

    // Per-widget state storage
    const widgetState = new WeakMap();

    // Initialize one SplitText widget instance
    function initSplitTextWidget(widget) {
        if (!widget) return;

        const animationType  = widget.dataset.animation;
        const animationStyle = widget.dataset.animationStyle;

        if (!animationType || animationType === "none") return;

        // Cleanup previous instance (important for refresh / re-render)
        const prev = widgetState.get(widget);
        if (prev) {
            prev.triggers.forEach(st => st && st.kill && st.kill());
            if (prev.split && prev.split.revert) prev.split.revert();
            widgetState.delete(widget);
        }

        // Initialize GSAP SplitText
        const split = new SplitText(widget, { type: animationType });

        let targets;
        switch (animationType) {
            case "chars":
                targets = split.chars;
                break;
            case "words":
                targets = split.words;
                break;
            case "lines":
                targets = split.lines;
                break;
            default:
                split.revert();
                return;
        }

        if (!targets || !targets.length) {
            split.revert();
            return;
        }

        // Animation presets
        let animProps, initialProps;
        switch (animationStyle) {
            case "1":
                animProps = { x: 0, opacity: 1, duration: 0.7, ease: "power4", stagger: 0.04 };
                initialProps = { x: 150, opacity: 0 };
                break;
            case "2":
                animProps = { y: 0, rotation: 0, opacity: 1, duration: 0.7, ease: "back", stagger: 0.15 };
                initialProps = { y: -100, rotation: "random(-80,80)", opacity: 0 };
                break;
            case "3":
                animProps = { rotationX: 0, opacity: 1, duration: 0.8, ease: "power3", stagger: 0.25 };
                initialProps = { rotationX: -100, transformOrigin: "50% 50% -160px", opacity: 0 };
                break;
            default:
                animProps = { x: 0, opacity: 1, duration: 0.7, ease: "power4", stagger: 0.04 };
                initialProps = { x: 150, opacity: 0 };
        }

        // Set initial styles immediately
        gsap.set(targets, initialProps);

        // Create scroll-triggered animation
        const tween = gsap.to(targets, {
            ...animProps,
            scrollTrigger: {
                trigger: widget,
                start: "top 80%",
                toggleActions: "play none none none",
                invalidateOnRefresh: true
            }
        });

        // Store widget state
        widgetState.set(widget, {
            split,
            triggers: tween.scrollTrigger ? [tween.scrollTrigger] : []
        });
    }

    // Init all widgets on page
    document.querySelectorAll(".ecw-SplitText-widget").forEach(initSplitTextWidget);

    // Single safe refresh after layout settles
    requestAnimationFrame(() => {
        if (typeof ScrollTrigger.refresh === "function") {
            ScrollTrigger.refresh();
        }
    });

});