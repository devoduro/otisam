{{-- Top bar: quizapp structure, jhs_portal look (white h-14 bar, menu toggles, user menu on the right). --}}
<header class="topbar">
    <div class="topbar-left d-flex align-items-center">
        <button class="btn-icon d-lg-none me-2" id="mobile-toggle" type="button" aria-label="Open menu">
            <i class="fas fa-bars fs-5" aria-hidden="true"></i>
        </button>
        <button class="btn-icon d-none d-lg-grid me-2" id="sidebar-toggle" type="button" aria-label="Collapse menu">
            <i class="fas fa-bars-staggered" aria-hidden="true"></i>
        </button>
        <div class="page-title-box d-lg-none">
            <h1 class="h6 fw-semibold mb-0 text-dark text-truncate" style="max-width:55vw">@yield('title', 'Dashboard')</h1>
        </div>
        <span class="d-none d-lg-inline small text-muted">Admin console</span>
    </div>

    <div class="topbar-right d-flex align-items-center gap-3">
        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="small text-muted text-decoration-none d-none d-md-inline">
            <i class="fas fa-arrow-up-right-from-square me-1" aria-hidden="true"></i> Live site
        </a>
        <div class="dropdown">
            <button class="btn btn-link text-decoration-none d-flex align-items-center gap-2 p-1 text-dark" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account menu for {{ auth()->user()->name }}">
                <span class="d-grid rounded-circle" style="width:32px;height:32px;place-items:center;background:#e0f2fe;color:#0284c7"><i class="fas fa-user" aria-hidden="true"></i></span>
                <span class="d-none d-md-inline small fw-medium">{{ auth()->user()->name }}</span>
                <i class="fas fa-chevron-down d-none d-md-inline text-muted" style="font-size:.65rem" aria-hidden="true"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border">
                <li class="dropdown-header small">{{ auth()->user()->roles->pluck('name')->join(', ') }}</li>
                <li><a class="dropdown-item small" href="{{ route('password.change') }}"><i class="fas fa-key me-2" aria-hidden="true"></i> Change password</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">@csrf<button class="dropdown-item small" type="submit"><i class="fas fa-sign-out-alt me-2" aria-hidden="true"></i> Logout</button></form>
                </li>
            </ul>
        </div>
    </div>
</header>
