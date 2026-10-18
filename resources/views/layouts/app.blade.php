<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('title')@yield('title') | @endif{{ config('app.name') }}</title>
    <meta name="description" content="@yield('description', 'Learn, practise and pass: SHS lessons, WASSCE-style practice questions and worked solutions for students in Ghana.')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen flex flex-col">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-2 focus:top-2 focus:z-50 focus:rounded focus:bg-white focus:px-3 focus:py-2">Skip to content</a>

    <header class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg font-extrabold text-primary-900 no-underline">
                <span class="grid h-9 w-9 place-items-center rounded-lg bg-primary-900 text-accent-500">{{ mb_substr(config('app.name'), 0, 1) }}</span>
                {{ config('app.name') }}
            </a>
            <nav aria-label="Account" class="flex items-center gap-1 text-sm font-semibold">
                <a href="{{ route('library.index') }}" class="rounded-lg px-3 py-2 text-primary-900 no-underline hover:bg-primary-50">Library</a>
                <a href="{{ route('subjects.index') }}" class="hidden rounded-lg px-3 py-2 text-primary-900 no-underline hover:bg-primary-50 sm:inline-block">Subjects</a>
                @auth
                    <a href="{{ route(auth()->user()->homeRoute()) }}" class="rounded-lg px-3 py-2 text-primary-900 no-underline hover:bg-primary-50 whitespace-nowrap">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg px-3 py-2 text-muted hover:bg-gray-100 whitespace-nowrap">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-primary-900 no-underline hover:bg-primary-50">Sign in</a>
                    <a href="{{ route('register') }}" class="rounded-lg bg-primary-900 px-3 py-2 text-white no-underline hover:bg-primary-600">Sign up</a>
                @endauth
            </nav>
        </div>
    </header>

    <main id="main" class="flex-1">
        @yield('content')
    </main>

    <footer class="border-t border-gray-200 bg-white">
        <div class="mx-auto flex max-w-6xl flex-col gap-2 px-4 py-6 text-sm text-muted sm:flex-row sm:justify-between">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
            <nav aria-label="Legal" class="flex gap-4">
                <a href="{{ route('terms') }}">Terms</a>
                <a href="{{ route('privacy') }}">Privacy</a>
                <a href="{{ route('subjects.index') }}" class="sm:hidden">Subjects</a>
                <a href="{{ route('plans.index') }}">Premium</a>
                <a href="{{ route('copyright') }}">Copyright</a>
                <a href="{{ route('teachers.register') }}">For teachers</a>
            </nav>
        </div>
    </footer>
</body>
</html>
