document.addEventListener("DOMContentLoaded", function () {

    if (typeof window.gsap === "undefined") return;
    if (typeof window.ScrollTrigger === "undefined" || typeof window.SplitText === "undefined") return;

    const { gsap } = window;
    gsap.registerPlugin(ScrollTrigger, SplitText);

    const widgetState = new WeakMap();

    function initTextOnScroll(widget) {
        if (!widget) return;

        // Clean up previous instance
        if (widgetState.has(widget)) {
            widgetState.get(widget).context.revert();
            widgetState.delete(widget);
        }

        const animType = widget.dataset.animation;
        if (!animType) return;

        // Get duration & stagger from data attributes, with defaults
        const duration = parseFloat(widget.dataset.duration) || 0.3;
        const stagger  = parseFloat(widget.dataset.stagger)  || 0.03;

        const ctx = gsap.context(() => {

            let split;
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
                        start: "top 70%",
                        toggleActions: "play reverse play reverse",
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
                        start: "top 70%",
                        toggleActions: "play reverse play reverse",
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
                        start: "top 70%",
                        toggleActions: "play reverse play reverse",
                        invalidateOnRefresh: true
                    }
                });
            }

        }, widget);

        widgetState.set(widget, { context: ctx });
    }

    document.querySelectorAll(".ecw-text-on-scroll").forEach(initTextOnScroll);

    requestAnimationFrame(() => {
        ScrollTrigger.refresh();
    });

});