<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'StockSync') }}</title>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!-- <script src="{{ mix('js/app.js') }}" defer></script> -->
    <script src="{{ asset('vendor/zxing/zxing.min.js') }}" type="text/javascript"></script>
    <!-- NProgress JS -->
    <script src="{{ asset('vendor/nprogress/nprogress.min.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <!-- NProgress CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/nprogress/nprogress.min.css') }}" />
    
    <!-- PWA Manifest & Meta -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#4f46e5">
    <link rel="apple-touch-icon" href="{{ asset('images/icon-192.png') }}">

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/staff-design.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('css/custom-auth.css') }}?v={{ time() }}" rel="stylesheet">
    <link href="{{ asset('css/admin-mobile.css') }}?v={{ time() }}" rel="stylesheet">
    @stack('styles')
    
    <style>
        /* Prevent layout shift by forcing scrollbar */
        html { overflow-y: scroll; }

        /* Remove NProgress spinner (top-right) to prevent nav jitter on page switches */
        #nprogress .spinner {
            display: none !important;
        }

        /* Keep admin/staff pages tight below top navbar */
        body.admin-layout main.py-4,
        body.staff-layout main.py-4 {
            padding-top: 0.5rem !important;
        }

        /* Fast app-like page transition for full-page navigation */
        #app main {
            opacity: 1;
            transform: translateY(0);
            filter: blur(0);
            transition: opacity 100ms ease-out, transform 100ms ease-out, filter 100ms ease-out;
            will-change: opacity, transform, filter;
        }
        body.page-leaving #app main {
            opacity: 0.9;
            transform: translateY(1px);
            filter: blur(0.35px);
        }

        /* Fast Bootstrap tab switch transition */
        .tab-content {
            position: relative;
        }
        .tab-content > .tab-pane {
            transition: opacity 70ms linear;
            transform: none !important;
        }
        .tab-content > .tab-pane.fade {
            transition: opacity 70ms linear !important;
        }

        /* ══════ CRITICAL MOBILE CSS - Prevent FOUC ══════ */
        /* Hide bottom nav on desktop */
        .admin-bottom-nav,
        .staff-bottom-nav {
            display: none;
        }
        
        /* Mobile: Style bottom nav immediately */
        @media (max-width: 767.98px) {
            .admin-bottom-nav,
            .staff-bottom-nav {
                display: flex !important;
                position: fixed !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                z-index: 1040 !important;
                background: rgba(255, 255, 255, 0.95) !important;
                backdrop-filter: blur(20px) !important;
                -webkit-backdrop-filter: blur(20px) !important;
                border-top: 0.5px solid rgba(0, 0, 0, 0.06) !important;
                box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.06) !important;
                padding: 0 !important;
                padding-bottom: env(safe-area-inset-bottom, 0px) !important;
            }
            .admin-bottom-nav-inner,
            .staff-bottom-nav-inner {
                display: flex !important;
                align-items: stretch !important;
                justify-content: space-around !important;
                width: 100% !important;
            }
            .admin-bottom-nav .bottom-nav-item,
            .staff-bottom-nav .bottom-nav-item {
                flex: 1 !important;
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                justify-content: center !important;
                text-decoration: none !important;
                color: #94a3b8 !important;
                font-size: 0.65rem !important;
                font-weight: 600 !important;
                padding: 0.6rem 0.25rem 0.5rem !important;
                gap: 0.2rem !important;
            }
            .admin-bottom-nav .bottom-nav-item i,
            .staff-bottom-nav .bottom-nav-item i {
                font-size: 1.2rem !important;
            }
            .admin-bottom-nav .bottom-nav-item.is-active,
            .staff-bottom-nav .bottom-nav-item.is-active {
                color: #4f46e5 !important;
            }
            .bottom-nav-home {
                flex: 1 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
            .bottom-nav-home-btn {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                width: 54px !important;
                height: 54px !important;
                border-radius: 50% !important;
                background: linear-gradient(145deg, #4f46e5 0%, #7c3aed 50%, #6366f1 100%) !important;
                color: #fff !important;
                font-size: 1.25rem !important;
                text-decoration: none !important;
                box-shadow: 0 4px 20px rgba(79, 70, 229, 0.45) !important;
                border: 3.5px solid #fff !important;
                margin-top: -28px !important;
            }
            /* Ensure body has padding for fixed bottom nav */
            body.admin-layout,
            body.staff-layout {
                padding-bottom: 85px !important;
            }
        }
        /* ══════ END CRITICAL MOBILE CSS ══════ */
    </style>

    <script>
        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('SW registered:', registration);
                    })
                    .catch(registrationError => {
                        console.log('SW registration failed:', registrationError);
                    });
            });
        }

        // Init NProgress
        window.addEventListener('load', () => {
            NProgress.done();
        });
        
        document.addEventListener('DOMContentLoaded', () => {
            NProgress.configure({ showSpinner: false });
            NProgress.start();

            // Bind to clicks for instant feedback
            document.querySelectorAll('a:not([target="_blank"]):not([href^="#"])').forEach(link => {
                link.addEventListener('click', (event) => {
                    // Ignore modified clicks/new-tab behavior and non-HTTP links
                    if (
                        event.defaultPrevented ||
                        event.metaKey || event.ctrlKey || event.shiftKey || event.altKey ||
                        link.hasAttribute('download') ||
                        link.getAttribute('rel') === 'external'
                    ) {
                        return;
                    }

                    // Skip links explicitly used as AJAX filters/actions
                    if (
                        link.hasAttribute('data-period') ||
                        link.hasAttribute('data-ajax') ||
                        link.closest('#periodFilterGroup')
                    ) {
                        return;
                    }

                    const href = link.getAttribute('href') || '';
                    if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) {
                        return;
                    }

                    // Respect links handled by Bootstrap JS toggles
                    if (link.hasAttribute('data-bs-toggle')) {
                        return;
                    }

                    // Add a short exit transition for same-origin page navigation
                    const destination = new URL(link.href, window.location.origin);
                    if (destination.origin === window.location.origin) {
                        event.preventDefault();
                        document.body.classList.add('page-leaving');
                        NProgress.start();
                        setTimeout(() => {
                            window.location.href = destination.href;
                        }, 70);
                        return;
                    }

                    NProgress.start();
                });
            });
            
            // Bind to forms
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', () => {
                    NProgress.start();
                });
            });

        });
    </script>
</head>
<body class="bg-light {{ auth()->check() && auth()->user()->isAdmin() ? 'admin-layout' : '' }} {{ auth()->check() && auth()->user()->isStaff() ? 'staff-layout' : '' }}">
    <div id="app">
        @auth
            @if(auth()->user()->isAdmin())
                @include('layouts.nav-admin')
            @elseif(auth()->user()->isStaff())
                @include('layouts.nav-staff')
            @endif
        @else
            @include('layouts.nav-guest')
        @endauth

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    @stack('modals')
    @stack('scripts')
</body>
</html>
