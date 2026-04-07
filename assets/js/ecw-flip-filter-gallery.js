document.addEventListener("DOMContentLoaded", function () {
    if (typeof gsap === "undefined" || typeof Flip === "undefined") return;

    const parents = document.querySelectorAll('.ecw-flip-filter-gallery-parent');

    parents.forEach(parent => {
        const allCheckbox = parent.querySelector('.filter-all');
        const filters = parent.querySelectorAll('.filter');
        const items = parent.querySelectorAll('.ecw-flip-filter-gallery-item');
        const container = parent.querySelector('.ecw-flip-filter-gallery-content');

        // --- Helper: calculate container height for visible items ---
        function getFlexContainerHeight(container) {
            const visibleItems = Array.from(container.children).filter(i => i.style.display !== 'none');
            if (visibleItems.length === 0) return 0;

            let rows = [];
            let currentRowTop = visibleItems[0].offsetTop;
            let currentRow = [];

            visibleItems.forEach(el => {
                if (el.offsetTop !== currentRowTop) {
                    rows.push(currentRow);
                    currentRow = [];
                    currentRowTop = el.offsetTop;
                }
                currentRow.push(el);
            });
            rows.push(currentRow);

            const gap = parseInt(getComputedStyle(container).gap) || 0;
            const totalHeight = rows.reduce((sum, row) => {
                const maxRowHeight = Math.max(...row.map(el => el.offsetHeight));
                return sum + maxRowHeight;
            }, 0);
            return totalHeight + gap * (rows.length - 1);
        }

        // --- Function to set container height for Approach2 ---
        function updateApproach2Height() {
            if (parent.classList.contains('ecw-approach2')) {
                const visibleHeight = getFlexContainerHeight(container);
                gsap.to(container, { height: visibleHeight, duration: 0.6, ease: "power1.inOut" });
            }
        }

        // --- Filter update ---
        const updateFilters = () => {
            const state = Flip.getState(items);
            const activeFilters = Array.from(filters).filter(f => f.checked);

            // Smart "All" logic
            if (activeFilters.length === filters.length) {
                allCheckbox.checked = true;
                filters.forEach(f => f.checked = false);
            }
            if (activeFilters.length > 0 && activeFilters.length < filters.length) {
                allCheckbox.checked = false;
            }

            // Toggle active class
            filters.forEach(f => {
                const label = f.closest('label');
                if (!label) return;
                label.classList.toggle('ecw-active', f.checked);
            });
            const allLabel = allCheckbox.closest('label');
            if (allLabel) allLabel.classList.toggle('ecw-active', allCheckbox.checked);

            // Show/hide items
            items.forEach(item => {
                const itemTag = Array.from(item.classList)
                    .find(cls => cls !== 'ecw-flip-filter-gallery-item');
                const show = allCheckbox.checked || activeFilters.some(f => f.dataset.filter === itemTag);
                item.style.display = show ? 'inline-flex' : 'none';
            });

            // Animate Approach2 height only
            updateApproach2Height();

            // GSAP Flip animation for items
            Flip.from(state, {
                duration: 0.6,
                scale: true,
                ease: "power1.inOut",
                absolute: true,
                stagger: 0.05,
                onEnter: elements =>
                    gsap.fromTo(elements,
                        { opacity: 0, scale: 0 },
                        { opacity: 1, scale: 1, duration: 0.5 }
                    ),
                onLeave: elements =>
                    gsap.to(elements, { opacity: 0, scale: 0, duration: 0.5 })
            });
        };

        // --- Event listeners ---
        filters.forEach(f => f.addEventListener('change', updateFilters));
        allCheckbox.addEventListener('change', () => {
            if (allCheckbox.checked) filters.forEach(f => f.checked = false);
            updateFilters();
        });

        // --- Initialize ---
        allCheckbox.checked = true;
        filters.forEach(f => f.checked = false);

        // --- Set initial Approach1 min-height once ---
        if (parent.classList.contains('ecw-approach1')) {
            const fullHeight = getFlexContainerHeight(container);
            container.style.minHeight = fullHeight + 'px';
        }

        // --- Set initial Approach2 height ---
        updateApproach2Height();

        // --- Initial filter update ---
        updateFilters();

        // --- Responsive: recalc Approach2 height on resize ---
        window.addEventListener('resize', () => {
            updateApproach2Height();
        });
    });
});