const normalizePath = (value) => {
    try {
        const url = new URL(value, window.location.origin);
        const normalized = url.pathname.replace(/\/+$/, '');
        return normalized === '' ? '/' : normalized;
    } catch (_) {
        return '/';
    }
};

const parsePublicModuleUrls = (navbar) => {
    try {
        const urls = JSON.parse(navbar.dataset.publicModuleUrls ?? '[]');
        return Array.isArray(urls) ? urls : [];
    } catch (_) {
        return [];
    }
};

const initPublicHeader = () => {
    const navbar = document.querySelector('.sp-public-navbar');

    if (!navbar || navbar.dataset.animationChecked === 'true') {
        return;
    }

    navbar.dataset.animationChecked = 'true';

    const publicModulePaths = new Set(parsePublicModuleUrls(navbar).map((url) => normalizePath(url)));
    const currentUrl = new URL(window.location.href);
    const forceNavbarAnimation = currentUrl.searchParams.get('sp_navbar_anim') === '1';

    const navigationEntry = performance.getEntriesByType('navigation')[0];
    const navigationType = navigationEntry?.type ?? 'navigate';
    const isReload = navigationType === 'reload';

    let shouldAnimate = isReload || forceNavbarAnimation;

    if (!shouldAnimate) {
        if (!document.referrer) {
            shouldAnimate = true;
        } else {
            try {
                const referrerUrl = new URL(document.referrer);
                const sameOrigin = referrerUrl.origin === window.location.origin;
                const referrerPath = normalizePath(referrerUrl.href);

                shouldAnimate = !sameOrigin || !publicModulePaths.has(referrerPath);
            } catch (_) {
                shouldAnimate = true;
            }
        }
    }

    if (!shouldAnimate) {
        return;
    }

    const runAnimation = () => {
        navbar.classList.remove('animate__animated', 'animate__backInDown');
        void navbar.offsetWidth;
        navbar.classList.add('animate__animated', 'animate__backInDown');
        navbar.style.setProperty('--animate-duration', '900ms');

        if (forceNavbarAnimation) {
            currentUrl.searchParams.delete('sp_navbar_anim');
            const nextUrl = `${currentUrl.pathname}${currentUrl.search ? currentUrl.search : ''}${currentUrl.hash}`;
            window.history.replaceState(window.history.state, '', nextUrl);
        }
    };

    if (document.readyState === 'complete') {
        runAnimation();
    } else {
        window.addEventListener('load', runAnimation, { once: true });
    }
};

initPublicHeader();
