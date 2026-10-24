<!DOCTYPE html>
{{-- Admin console: quizapp's shell structure, styled like castvote (see assets/admin/css/otisam-theme.css). --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/brand/otisam-mark.svg') }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin') | {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/vendor/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-topbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-empty-state.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/otisam-theme.css') }}">
    @stack('styles')
</head>
<body>
<script>
    try { if (localStorage.getItem('ot_sidebar_state') === 'collapsed') document.body.classList.add('sidebar-collapsed'); } catch (e) {}
</script>

<a href="#main" class="visually-hidden-focusable position-absolute top-0 start-0 m-2 btn btn-light" style="z-index:2000">Skip to content</a>
<div class="sidebar-overlay"></div>

<div class="app-layout">
    @include('admin.partials.sidebar')

    <div class="main-content">
        @include('admin.partials.topbar')

        <main id="main" class="content-pad">
            <div class="page-heading d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h2>@yield('title', 'Dashboard')</h2>
                    @hasSection('subtitle')<p>@yield('subtitle')</p>@endif
                </div>
                @hasSection('actions')
                    <div class="d-flex flex-wrap gap-2">@yield('actions')</div>
                @endif
            </div>
            @include('admin.partials.flash')
            @yield('content')
        </main>

        <footer class="px-4 py-3 border-top small text-muted d-flex flex-wrap justify-content-between gap-2">
            <span>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</span>
            <a href="{{ route('home') }}" target="_blank" rel="noopener" class="text-muted">Open the public site</a>
        </footer>
    </div>
</div>

<script src="{{ asset('assets/admin/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/admin.js') }}"></script>
@stack('scripts')
</body>
</html>
