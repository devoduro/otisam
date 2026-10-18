<!DOCTYPE html>
{{-- Admin console shell, ported from quizapp's layouts/admin.blade.php --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin') | {{ config('app.name') }}</title>

    <link rel="stylesheet" href="{{ asset('assets/admin/fonts/plus-jakarta/stylesheet.css') }}">
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

    <script>
        try { document.documentElement.setAttribute('data-bs-theme', localStorage.getItem('ot_theme') || 'light'); } catch (e) {}
    </script>
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
            @hasSection('actions')
                <div class="d-flex d-xxl-none flex-wrap gap-2 justify-content-end mb-3">@yield('actions')</div>
            @endif
            @include('admin.partials.flash')
            @yield('content')
        </main>
    </div>
</div>

<script src="{{ asset('assets/admin/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/admin.js') }}"></script>
@stack('scripts')
</body>
</html>
