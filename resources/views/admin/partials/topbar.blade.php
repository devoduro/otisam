{{-- Top bar from quizapp's partials/admin-topbar.blade.php (language switcher and add-ons removed). --}}
<header class="topbar">
    <div class="topbar-left d-flex align-items-center">
        <button class="btn-icon d-lg-none me-3" id="mobile-toggle" type="button" aria-label="Open menu">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <button class="btn-icon d-none d-lg-grid me-3" id="sidebar-toggle" type="button" aria-label="Collapse menu">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
        </button>
        <div class="page-title-box">
            <h1 class="h5 fw-bold mb-0 text-dark">@yield('title', 'Dashboard')</h1>
            @hasSection('subtitle')
                <p class="small text-muted mb-0 d-none d-md-block">@yield('subtitle')</p>
            @endif
        </div>
    </div>

    <div class="topbar-right d-flex align-items-center gap-2 gap-sm-3">
        @hasSection('actions')
            {{-- Wide screens only; narrower screens get the same buttons above the content (layouts/admin). --}}
            <div class="d-none d-xxl-flex align-items-center gap-2">@yield('actions')</div>
        @endif

        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn-visit-site d-none d-lg-flex">
            <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
            <span>Live site</span>
        </a>

        <button class="theme-switch-wrapper border-0 bg-transparent p-0" id="theme-toggle" type="button" aria-label="Switch light or dark theme">
            <span class="theme-switch">
                <svg class="switch-icon-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                <svg class="switch-icon-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/></svg>
            </span>
        </button>

        <div class="dropdown">
            <button class="btn btn-light rounded-pill d-flex align-items-center gap-2 px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account menu for {{ auth()->user()->name }}">
                <i class="fa-solid fa-user d-sm-none" aria-hidden="true"></i>
                <span class="fw-semibold small d-none d-sm-inline">{{ auth()->user()->name }}</span>
                <i class="fa-solid fa-chevron-down small" aria-hidden="true"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                <li class="dropdown-header small">{{ auth()->user()->roles->pluck('name')->join(', ') }}</li>
                <li><a class="dropdown-item" href="{{ route('password.change') }}">Change password</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">@csrf<button class="dropdown-item text-danger" type="submit">Log out</button></form>
                </li>
            </ul>
        </div>
    </div>
</header>
