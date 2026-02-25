<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm sticky-top">
    <div class="container-fluid px-4 px-lg-5">
        <a class="navbar-brand fw-bold d-flex align-items-center fs-4" href="{{ url('/') }}">
            <div class="bg-primary text-white rounded p-2 me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <span class="text-primary">{{ config('app.name', 'StockSync') }}</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <i class="fa-solid fa-bars text-dark"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Centered Links -->
            <ul class="navbar-nav mx-auto gap-1 gap-lg-4 align-items-start align-items-md-center mb-3 mb-md-0">
                <li class="nav-item w-100 w-md-auto">
                    <a class="nav-link fw-semibold d-flex align-items-center gap-2 py-2 px-3 rounded-pill {{ request()->routeIs('staff.dashboard') ? 'active bg-primary text-white shadow-sm' : 'text-secondary hover-bg-light' }}" href="{{ route('staff.dashboard') }}">
                        <i class="fa-solid fa-chart-line" style="width: 20px; text-align: center;"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item w-100 w-md-auto">
                    <a class="nav-link fw-semibold d-flex align-items-center gap-2 py-2 px-3 rounded-pill {{ request()->routeIs('staff.pos') ? 'active bg-primary text-white shadow-sm' : 'text-secondary hover-bg-light' }}" href="{{ route('staff.pos') }}">
                        <i class="fa-solid fa-cash-register" style="width: 20px; text-align: center;"></i> POS
                    </a>
                </li>
                <li class="nav-item w-100 w-md-auto">
                    <a class="nav-link fw-semibold d-flex align-items-center gap-2 py-2 px-3 rounded-pill {{ request()->routeIs('staff.inventory') ? 'active bg-primary text-white shadow-sm' : 'text-secondary hover-bg-light' }}" href="{{ route('staff.inventory') }}">
                        <i class="fa-solid fa-boxes-stacked" style="width: 20px; text-align: center;"></i> Inventory
                    </a>
                </li>
                <li class="nav-item w-100 w-md-auto">
                    <a class="nav-link fw-semibold d-flex align-items-center gap-2 py-2 px-3 rounded-pill {{ request()->routeIs('staff.logs') ? 'active bg-primary text-white shadow-sm' : 'text-secondary hover-bg-light' }}" href="{{ route('staff.logs') }}">
                        <i class="fa-solid fa-clipboard-list" style="width: 20px; text-align: center;"></i> Logs
                    </a>
                </li>
            </ul>

            <!-- Right Side Profile -->
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle fw-bold d-flex align-items-center py-1 px-3 rounded-pill bg-light border" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm" style="width: 32px; height: 32px; font-size: 0.8rem;">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <span class="text-dark">{{ Auth::user()->name }}</span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 rounded-3" aria-labelledby="navbarDropdown">
                        <div class="px-3 py-2 border-bottom mb-2 bg-light rounded-top-3">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Signed in as</small>
                            <div class="fw-bold text-dark">{{ Auth::user()->email }}</div>
                        </div>
                        <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                            <i class="fa-solid fa-user me-2 text-primary"></i> {{ __('Profile') }}
                        </a>
                        <hr class="dropdown-divider">
                        <a class="dropdown-item py-2 text-danger" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa-solid fa-right-from-bracket me-2"></i> {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
    .hover-bg-light:hover {
        background-color: #f8f9fa;
        color: #0d6efd !important;
    }
</style>