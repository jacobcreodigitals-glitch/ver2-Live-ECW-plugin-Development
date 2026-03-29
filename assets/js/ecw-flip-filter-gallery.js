document.addEventListener("DOMContentLoaded", function () {
    if (typeof gsap === "undefined" || typeof Flip === "undefined") return;

    const parents = document.querySelectorAll('.ecw-flip-filter-gallery-parent');

    parents.forEach(parent => {
        const allCheckbox = parent.querySelector('.filter-all');
        const filters = parent.querySelectorAll('.filter');
        const items = parent.querySelectorAll('.ecw-flip-filter-gallery-item');

        const updateFilters = () => {
            const state = Flip.getState(items);

            // Get all checked filter slugs
            const activeFilters = Array.from(filters)
                .filter(f => f.checked)
                .map(f => f.dataset.filter);

            // Determine which items should show
            items.forEach(item => {
                const show = activeFilters.length === 0 || activeFilters.includes(
                    Array.from(item.classList).find(cls => cls !== 'ecw-flip-filter-gallery-item')
                );
                item.style.display = show ? 'inline-flex' : 'none';
            });

            // Animate with Flip
            Flip.from(state, {
                duration: 0.6,
                scale: true,
                ease: "power1.inOut",
                absolute: true,
                stagger: 0.05,
                onEnter: elements => gsap.fromTo(elements, {opacity: 0, scale: 0}, {opacity: 1, scale: 1, duration: 0.5}),
                onLeave: elements => gsap.to(elements, {opacity: 0, scale: 0, duration: 0.5})
            });

            // Update "All" checkbox
            const visibleCount = Array.from(items).filter(item => item.style.display !== 'none').length;
            allCheckbox.checked = visibleCount === items.length;
        };

        // Event listeners
        filters.forEach(f => f.addEventListener('change', updateFilters));
        allCheckbox.addEventListener('change', () => {
            const check = allCheckbox.checked;
            filters.forEach(f => f.checked = check);
            updateFilters();
        });

        // Initial animation
        updateFilters();
    });
});