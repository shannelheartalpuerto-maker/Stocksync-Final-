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
    <link href="{{ asset('css/custom-auth.css') }}?v={{ time() }}" rel="stylesheet">
    @stack('styles')
    
    <style>
        /* Prevent layout shift by forcing scrollbar */
        html { overflow-y: scroll; }
        
        /* Stat Card Gradient Design */
        .stat-card {
            background: #ffffff;
            border: 0 !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05) !important;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
            border-radius: 12px;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
        }
        
        /* Gradient Accents - Full Colored Backgrounds (Slightly more visible) */
        .card-gradient-primary { background: linear-gradient(135deg, #e3f2fd 0%, #90caf9 100%) !important; color: #0c4a6e; border-bottom: 0 !important; }
        .card-gradient-success { background: linear-gradient(135deg, #e8f5e9 0%, #a5d6a7 100%) !important; color: #14532d; border-bottom: 0 !important; }
        .card-gradient-warning { background: linear-gradient(135deg, #fff8e1 0%, #ffe082 100%) !important; color: #713f12; border-bottom: 0 !important; }
        .card-gradient-info    { background: linear-gradient(135deg, #e0f7fa 0%, #80deea 100%) !important; color: #155e75; border-bottom: 0 !important; }
        .card-gradient-danger  { background: linear-gradient(135deg, #ffebee 0%, #ef9a9a 100%) !important; color: #7f1d1d; border-bottom: 0 !important; }

        /* Adjust icon wrapper to blend in */
        .card-gradient-primary .icon-wrapper { background: rgba(255, 255, 255, 0.5) !important; color: #0284c7 !important; }
        .card-gradient-success .icon-wrapper { background: rgba(255, 255, 255, 0.5) !important; color: #16a34a !important; }
        .card-gradient-warning .icon-wrapper { background: rgba(255, 255, 255, 0.5) !important; color: #d97706 !important; }
        .card-gradient-info    .icon-wrapper { background: rgba(255, 255, 255, 0.5) !important; color: #0891b2 !important; }
        .card-gradient-danger  .icon-wrapper { background: rgba(255, 255, 255, 0.5) !important; color: #dc2626 !important; }

        .icon-wrapper {
            box-shadow: inset 0 0 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        .stat-card:hover .icon-wrapper {
            transform: scale(1.1);
        }

        @media (max-width: 767.98px) {
            .bg-light-mobile {
                background-color: #f8f9fa;
            }
            /* Widen container on mobile for better space usage */
            .container {
                padding-left: 12px;
                padding-right: 12px;
            }
            /* Increase touch targets for inputs and buttons */
            .btn, .form-control, .form-select {
                min-height: 44px;
            }
            /* Better spacing for stacked columns */
            .row > div {
                margin-bottom: 0.5rem;
            }
            /* Fix navbar collapse alignment */
            .navbar-collapse {
                padding-top: 1rem;
            }
        }
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
            NProgress.start();
            
            // Bind to clicks for instant feedback
            document.querySelectorAll('a:not([target="_blank"]):not([href^="#"])').forEach(link => {
                link.addEventListener('click', () => {
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
<body class="bg-light">
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
