document.addEventListener("DOMContentLoaded", function () {

    if (typeof window.gsap === "undefined") return;
    if (typeof window.ScrollTrigger === "undefined" || typeof window.SplitText === "undefined") return;

    const { gsap } = window;
    gsap.registerPlugin(ScrollTrigger, SplitText);

    const widgetState = new WeakMap();

    function initTextOnScroll(widget) {
        if (!widget) return;

        let split = null; // always defined

        // Cleanup previous animation & ScrollTrigger
        if (widgetState.has(widget)) {
            const prev = widgetState.get(widget);
            if (prev.split) prev.split.revert();   // revert SplitText
            if (prev.context) prev.context.revert(); // revert GSAP context
            widgetState.delete(widget);
        }

        const animType = widget.dataset.animation;
        if (!animType) return;

        const duration = parseFloat(widget.dataset.duration) || 0.3;
        const stagger  = parseFloat(widget.dataset.stagger)  || 0.03;

        const ctx = gsap.context(() => {

            let elements = [];

            // -------------------------
            // Animation 1: Lines rotate stagger
            // -------------------------
            if (animType === "1") {
                split = new SplitText(widget, { type: "lines" });
                elements = split.lines;

                gsap.set(widget, { autoAlpha: 1 });
                gsap.set(elements, { rotationX: -100, opacity: 0, transformOrigin: "50% 50% -160px" });

                gsap.to(elements, {
                    rotationX: 0,
                    opacity: 1,
                    duration: duration,
                    ease: "power3.out",
                    stagger: stagger,
                    scrollTrigger: {
                        trigger: widget,
                        start: "top 80%",
                        toggleActions: "restart pause resume reverse",
                        invalidateOnRefresh: true
                    }
                });
            }

            // -------------------------
            // Animation 2: Words drop
            // -------------------------
            if (animType === "2") {
                split = new SplitText(widget, { type: "words" });
                elements = split.words;

                gsap.set(widget, { autoAlpha: 1 });
                gsap.set(elements, { y: 50, opacity: 0 });

                gsap.to(elements, {
                    y: 0,
                    opacity: 1,
                    duration: duration,
                    ease: "circ.out",
                    stagger: stagger,
                    scrollTrigger: {
                        trigger: widget,
                        start: "top 80%",
                        toggleActions: "restart pause resume reverse",
                        invalidateOnRefresh: true
                    }
                });
            }

            // -------------------------
            // Animation 3: Lines + words smooth flow
            // -------------------------
            if (animType === "3") {
                split = new SplitText(widget, { type: "lines" });
                const allWords = [];

                split.lines.forEach(line => {
                    const wrap = document.createElement("div");
                    wrap.classList.add("ecw-line-mask");
                    line.parentNode.insertBefore(wrap, line);
                    wrap.appendChild(line);

                    const wordSplit = new SplitText(line, { type: "words" });
                    allWords.push(...wordSplit.words);
                });

                gsap.set(widget, { autoAlpha: 1 });
                gsap.set(allWords, { y: 50, opacity: 0 });

                gsap.to(allWords, {
                    y: 0,
                    opacity: 1,
                    duration: duration,
                    ease: "circ.out",
                    stagger: stagger,
                    scrollTrigger: {
                        trigger: widget,
                        start: "top 80%",
                        // markers:true,
                        toggleActions: "restart pause resume reverse",
                        invalidateOnRefresh: true
                    }
                });
            }

        }, widget);

        // Save state for future cleanup
        widgetState.set(widget, { context: ctx, split });
    }

    // Initialize all widgets
    document.querySelectorAll(".ecw-text-on-scroll").forEach(initTextOnScroll);

    // Refresh ScrollTrigger after next frame to catch layout changes
    requestAnimationFrame(() => ScrollTrigger.refresh());

    // Ensure fonts/images are loaded in case SplitText depends on them
    window.addEventListener("load", () => {
        document.querySelectorAll(".ecw-text-on-scroll").forEach(initTextOnScroll);
        ScrollTrigger.refresh();
    });

});