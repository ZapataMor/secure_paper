@php
    $homeUrl = Route::has('home') ? route('home') : url('/');
    $loginUrl = Route::has('login') ? route('login') : '#';
    $servicesUrl = Route::has('services') ? route('services') : url('/servicios');
    $aboutUrl = Route::has('about') ? route('about') : url('/nosotros');
    $plansUrl = Route::has('plans') ? route('plans') : url('/planes');
@endphp

<header class="sp-public-navbar">
    <div class="sp-public-navbar-inner mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 text-[#F1F7F6]">
        <div class="flex items-center gap-3">
            <div class="rounded-lg bg-[#2CC295] p-2">
                <svg class="h-8 w-8 text-[#000F1F]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2 4 6v6c0 5 3.2 9.4 8 10 4.8-.6 8-5 8-10V6z" />
                    <path d="m9 12 2 2 4-4" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Professional Papers</h1>
                <p class="text-sm text-[#AAC8C4]">Academic Excellence & Trust</p>
            </div>
        </div>

        <nav class="flex flex-wrap items-center gap-3 sm:gap-6">
            <a href="{{ route('home') }}" class="font-medium text-[#F1F7F6] transition-colors hover:text-[#2CC295]">Inicio</a>
            <a href="{{ $servicesUrl }}" class="font-medium text-[#F1F7F6] transition-colors hover:text-[#2CC295]">Servicios</a>
            <a href="{{ $aboutUrl }}" class="font-medium text-[#F1F7F6] transition-colors hover:text-[#2CC295]">Nosotros</a>
            <a href="{{ $plansUrl }}" class="font-medium text-[#F1F7F6] transition-colors hover:text-[#2CC295]">Planes</a>
            <a href="{{ $loginUrl }}" class="rounded-lg bg-[#006A4C] px-6 py-2 font-semibold text-[#F1F7F6] transition-colors hover:bg-[#00BF81]">
                Iniciar Sesion
            </a>
        </nav>
    </div>
</header>

<script>
    (() => {
        const navbar = document.querySelector('.sp-public-navbar');

        if (!navbar) {
            return;
        }

        const normalizePath = (value) => {
            try {
                const url = new URL(value, window.location.origin);
                const normalized = url.pathname.replace(/\/+$/, '');
                return normalized === '' ? '/' : normalized;
            } catch (_) {
                return '/';
            }
        };

        const publicModuleUrls = @js([$homeUrl, $servicesUrl, $aboutUrl, $plansUrl]);
        const publicModulePaths = new Set(publicModuleUrls.map((url) => normalizePath(url)));
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
    })();
</script>
