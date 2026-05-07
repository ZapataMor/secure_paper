const normalizePath = (value) => {
    try {
        const url = new URL(value, window.location.origin);
        const normalized = url.pathname.replace(/\/+$/, '');
        return normalized === '' ? '/' : normalized;
    } catch (_) {
        return '/';
    }
};

const getVisibleModuleLinks = (nav) => {
    return Array.from(nav.querySelectorAll('.sp-admin-module-link')).filter((item) => item.offsetParent !== null);
};

const syncMobileLinks = (activeLink) => {
    const mobileLinks = Array.from(document.querySelectorAll('.sp-admin-mobile-panel .sp-admin-mobile-link'));

    mobileLinks.forEach((link) => {
        link.classList.remove('is-active');

        if (normalizePath(link.href) === normalizePath(activeLink.href)) {
            link.classList.add('is-active');
        }
    });
};

const findActiveLink = (links, forcedLink = null) => {
    if (forcedLink) {
        return forcedLink;
    }

    const currentPath = normalizePath(window.location.href);
    let bestMatch = null;
    let bestLength = -1;

    links.forEach((link) => {
        const path = normalizePath(link.href);
        const isExact = currentPath === path;
        const isNested = path !== '/' && currentPath.startsWith(`${path}/`);

        if ((isExact || isNested) && path.length > bestLength) {
            bestMatch = link;
            bestLength = path.length;
        }
    });

    return bestMatch || links[0];
};

const moveIndicator = (nav, indicator, target) => {
    if (!target) {
        return;
    }

    const navRect = nav.getBoundingClientRect();
    const targetRect = target.getBoundingClientRect();
    const left = targetRect.left - navRect.left;

    indicator.style.width = `${targetRect.width}px`;
    indicator.style.transform = `translate3d(${left}px, -50%, 0)`;
    indicator.style.opacity = '1';
};

const syncActiveLinks = (links, forcedLink = null) => {
    const activeLink = findActiveLink(links, forcedLink);

    links.forEach((link) => {
        link.classList.remove('is-active');
        link.removeAttribute('aria-current');
    });

    activeLink.classList.add('is-active');
    activeLink.setAttribute('aria-current', 'page');
    syncMobileLinks(activeLink);

    return activeLink;
};

const bindModulesDropdown = () => {
    const toggle = document.getElementById('spModulesToggle');
    const panel = document.getElementById('spModulesPanel');

    if (!toggle || !panel || toggle.dataset.bound === 'true') {
        return;
    }

    toggle.dataset.bound = 'true';

    toggle.addEventListener('click', () => {
        const isOpen = panel.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', String(isOpen));
    });

    panel.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            panel.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
        });
    });
};

const bindNavIndicator = () => {
    const nav = document.querySelector('.sp-admin-modules-nav');

    if (!nav) {
        return;
    }

    const links = getVisibleModuleLinks(nav);
    const indicator = nav.querySelector('.sp-admin-nav-indicator');

    if (!indicator || links.length === 0) {
        return;
    }

    nav.classList.add('has-indicator');
    moveIndicator(nav, indicator, syncActiveLinks(links));

    links.forEach((link) => {
        if (link.dataset.indicatorBound === 'true') {
            return;
        }

        link.dataset.indicatorBound = 'true';
        link.addEventListener('click', () => {
            syncActiveLinks(links, link);
            moveIndicator(nav, indicator, link);
        });
    });
};

const refreshIndicatorPosition = () => {
    const nav = document.querySelector('.sp-admin-modules-nav');
    const indicator = nav?.querySelector('.sp-admin-nav-indicator');
    const links = nav ? getVisibleModuleLinks(nav) : [];

    if (!nav || !indicator || links.length === 0) {
        return;
    }

    moveIndicator(nav, indicator, findActiveLink(links));
};

const initAdminNavbar = () => {
    bindModulesDropdown();
    bindNavIndicator();
};

initAdminNavbar();

if (window.__spAdminNavbarBound !== true) {
    window.__spAdminNavbarBound = true;
    document.addEventListener('livewire:navigated', initAdminNavbar);
    window.addEventListener('resize', refreshIndicatorPosition);
}
