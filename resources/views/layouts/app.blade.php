<!DOCTYPE html>
{{-- Public pages. Styling follows jhs_portal; the phone bottom tab bar is borrowed from examsghana.com. --}}
@php
    $navLinks = [
        ['Home', route('home'), 'fa-house', request()->routeIs('home')],
        ['Library', route('library.index'), 'fa-book-open', request()->routeIs('library.*')],
        ['Subjects', route('subjects.index'), 'fa-sitemap', request()->routeIs('subjects.*')],
        ['Premium', route('plans.index'), 'fa-crown', request()->routeIs('plans.*')],
    ];
    $accountUrl = auth()->check() ? route(auth()->user()->homeRoute()) : route('login');
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('title')@yield('title') | @endif{{ config('app.name') }}</title>
    <meta name="description" content="@yield('description', 'SHS past questions, worked solutions, curriculum topics and practice for students and teachers in Ghana.')">
    @include('partials.head-assets')
</head>
<body class="min-h-screen flex flex-col bg-gray-50 font-sans antialiased pb-16 md:pb-0">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-2 focus:top-2 focus:z-[60] focus:rounded focus:bg-white focus:px-3 focus:py-2">Skip to content</a>

    <header class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 md:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-2 no-underline">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg gradient-bg text-sm font-bold text-white">{{ mb_substr(config('app.name'), 0, 1) }}</span>
                <span class="text-xl font-bold gradient-text">{{ config('app.name') }}</span>
            </a>

            <nav aria-label="Main" class="hidden md:flex items-center gap-1 text-sm">
                @foreach ($navLinks as [$label, $href, $icon, $active])
                    <a href="{{ $href }}" @if ($active) aria-current="page" @endif
                       class="flex items-center gap-2 rounded-lg px-3 py-2 no-underline transition {{ $active ? 'bg-primary-50 text-primary-600 font-medium' : 'text-gray-600 hover:bg-primary-50 hover:text-primary-600' }}">
                        <i class="fas {{ $icon }}" aria-hidden="true"></i>{{ $label }}
                    </a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2 text-sm">
                @auth
                    <a href="{{ $accountUrl }}" class="hidden sm:inline-flex items-center gap-2 rounded-md bg-primary-600 px-4 py-2 font-semibold text-white no-underline hover:bg-primary-700 hover:text-white">
                        <i class="fas fa-tachometer-alt" aria-hidden="true"></i> Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button type="submit" class="rounded-md px-3 py-2 text-gray-600 hover:bg-gray-100" title="Logout"><i class="fas fa-sign-out-alt" aria-hidden="true"></i><span class="sr-only">Logout</span></button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-md px-3 py-2 font-medium text-gray-700 no-underline hover:bg-gray-100 hover:text-gray-800">Sign in</a>
                    <a href="{{ route('register') }}" class="rounded-md bg-primary-600 px-4 py-2 font-semibold text-white no-underline hover:bg-primary-700 hover:text-white">Sign up free</a>
                @endauth
            </div>
        </div>
    </header>

    <main id="main" class="flex-1">
        @yield('content')
    </main>

    <footer class="border-t border-gray-200 bg-white">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 md:grid-cols-4 md:px-6">
            <div class="md:col-span-2">
                <a href="{{ route('home') }}" class="flex items-center gap-2 no-underline">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg gradient-bg text-sm font-bold text-white">{{ mb_substr(config('app.name'), 0, 1) }}</span>
                    <span class="text-lg font-bold gradient-text">{{ config('app.name') }}</span>
                </a>
                <p class="mt-3 max-w-sm text-sm text-gray-500">Past questions, worked solutions and curriculum-mapped study material for SHS students and teachers in Ghana.</p>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-800">Learn</p>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><a href="{{ route('library.index') }}" class="text-gray-500 hover:text-primary-600">Library</a></li>
                    <li><a href="{{ route('subjects.index') }}" class="text-gray-500 hover:text-primary-600">Subjects &amp; topics</a></li>
                    <li><a href="{{ route('plans.index') }}" class="text-gray-500 hover:text-primary-600">Premium plans</a></li>
                    <li><a href="{{ route('teachers.register') }}" class="text-gray-500 hover:text-primary-600">For teachers</a></li>
                </ul>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-800">About</p>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><a href="{{ route('terms') }}" class="text-gray-500 hover:text-primary-600">Terms of Service</a></li>
                    <li><a href="{{ route('privacy') }}" class="text-gray-500 hover:text-primary-600">Privacy Policy</a></li>
                    <li><a href="{{ route('copyright') }}" class="text-gray-500 hover:text-primary-600">Copyright &amp; removal</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-200 py-4 px-6 text-center text-sm text-gray-500">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</div>
    </footer>

    <!-- Phone tab bar (examsghana-style) -->
    <nav aria-label="Quick links" class="fixed inset-x-0 bottom-0 z-40 grid grid-cols-5 border-t border-gray-200 bg-white md:hidden">
        @foreach ([...$navLinks, [auth()->check() ? 'Me' : 'Sign in', $accountUrl, 'fa-user', request()->routeIs('login', 'learn.*', 'teach.*')]] as [$label, $href, $icon, $active])
            <a href="{{ $href }}" @if ($active) aria-current="page" @endif
               class="flex flex-col items-center justify-center gap-0.5 py-2 text-[11px] no-underline {{ $active ? 'text-primary-600 font-semibold' : 'text-gray-500' }}">
                <i class="fas {{ $icon }} text-lg" aria-hidden="true"></i>{{ $label }}
            </a>
        @endforeach
    </nav>
</body>
</html>
