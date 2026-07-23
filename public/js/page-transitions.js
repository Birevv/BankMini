(() => {
    const exitDuration = 140;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let isLeaving = false;

    const resetPage = () => {
        isLeaving = false;
        document.documentElement.classList.remove('page-is-leaving');
    };

    window.addEventListener('pageshow', resetPage);

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');

        if (
            !link
            || event.defaultPrevented
            || event.button !== 0
            || event.metaKey
            || event.ctrlKey
            || event.shiftKey
            || event.altKey
            || reducedMotion.matches
            || isLeaving
            || link.hasAttribute('download')
            || link.hasAttribute('data-no-page-transition')
            || (link.target && link.target !== '_self')
        ) {
            return;
        }

        const destination = new URL(link.href, window.location.href);
        const staysOnCurrentSection = destination.pathname === window.location.pathname
            && destination.search === window.location.search
            && destination.hash;

        if (
            destination.origin !== window.location.origin
            || !['http:', 'https:'].includes(destination.protocol)
            || staysOnCurrentSection
        ) {
            return;
        }

        event.preventDefault();
        isLeaving = true;
        document.documentElement.classList.add('page-is-leaving');

        window.setTimeout(() => {
            window.location.assign(destination.href);
        }, exitDuration);
    });
})();
