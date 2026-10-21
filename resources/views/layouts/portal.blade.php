<!DOCTYPE html>
{{-- Student and teacher area, modelled on jhs_portal's student-app-layout. --}}
@php
    $user = auth()->user();
    $isTeacher = $user->isTeacher();
    $home = $isTeacher ? route('teach.dashboard') : route('learn.dashboard');
    $links = array_filter([
        ['Dashboard', $home, 'fa-tachometer-alt', request()->routeIs('learn.dashboard', 'teach.dashboard')],
        $isTeacher ? ['My classes', route('teach.classes.index'), 'fa-people-group', request()->routeIs('teach.classes.*', 'teach.assignments.*')] : ['My classes', route('learn.classes.index'), 'fa-people-group', request()->routeIs('learn.classes.*', 'learn.assignments.*')],
        $isTeacher ? ['My lesson plans', route('teach.lesson-plans.index'), 'fa-clipboard-list', request()->routeIs('teach.lesson-plans.*')] : null,
        $isTeacher ? ['My video tutorials', route('teach.videos.index'), 'fa-circle-play', request()->routeIs('teach.videos.*')] : null,
        $isTeacher ? ['Lesson materials', route('teach.materials.index'), 'fa-folder-open', request()->routeIs('teach.materials.*')] : null,
        $isTeacher ? ['My profile', route('teach.profile.edit'), 'fa-id-badge', request()->routeIs('teach.profile.*')] : null,
        ['Library', route('library.index'), 'fa-book-open', request()->routeIs('library.*')],
        ['Practice questions', route('practice.index'), 'fa-list-check', request()->routeIs('practice.*')],
        ['Video lessons', route('videos.index'), 'fa-circle-play', request()->routeIs('videos.*')],
        $isTeacher ? ['Lesson plans', route('lesson-plans.index'), 'fa-clipboard-list', request()->routeIs('lesson-plans.*')] : null,
        $isTeacher ? ['Subject apps', route('subject-apps.index'), 'fa-robot', request()->routeIs('subject-apps.*')] : null,
        ['Downloads', route('downloads.index'), 'fa-download', request()->routeIs('downloads.*')],
        $isTeacher ? ['Promotion exams', route('promotion'), 'fa-chalkboard-user', request()->routeIs('promotion')] : null,
        ['Subjects & topics', route('subjects.index'), 'fa-sitemap', request()->routeIs('subjects.*')],
        ['My subscription', route('account.subscription'), 'fa-crown', request()->routeIs('account.*')],
        ['Premium plans', route('plans.index'), 'fa-tags', request()->routeIs('plans.*')],
        ['Change password', route('password.change'), 'fa-key', request()->routeIs('password.change')],
    ]);
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('title')@yield('title') | @endif{{ config('app.name') }}</title>
    @include('partials.head-assets')
</head>
<body class="font-sans antialiased bg-gray-50">
<a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-2 focus:top-2 focus:z-[60] focus:rounded focus:bg-white focus:px-3 focus:py-2">Skip to content</a>
<div x-data="{ sidebarOpen: false }" class="min-h-screen flex">
    <!-- Mobile backdrop -->
    <div x-show="sidebarOpen" x-cloak x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-black/50 md:hidden"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 w-72 -translate-x-full bg-white border-r border-gray-200 shadow-lg transform transition-transform duration-300 ease-in-out md:static md:w-64 md:translate-x-0 md:shadow-none md:min-h-screen flex-shrink-0"
           aria-label="Main menu">
        <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
            <a href="{{ $home }}" class="flex items-center gap-2 no-underline">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg gradient-bg text-sm font-bold text-white">{{ mb_substr(config('app.name'), 0, 1) }}</span>
                <span class="text-xl font-bold gradient-text">{{ config('app.name') }}</span>
            </a>
            <button type="button" @click="sidebarOpen = false" class="md:hidden text-gray-500 hover:text-gray-700" aria-label="Close menu"><i class="fas fa-times text-lg" aria-hidden="true"></i></button>
        </div>

        <nav class="p-4 space-y-1">
            @foreach ($links as [$label, $href, $icon, $active])
                <a href="{{ $href }}" @if ($active) aria-current="page" @endif
                   class="flex items-center gap-3 px-4 py-3 rounded-xl no-underline transition-all duration-200 {{ $active ? 'bg-gold-50 text-gold-700 font-semibold' : 'font-medium text-primary-700 hover:bg-gold-50 hover:text-gold-700' }}">
                    <i class="fas {{ $icon }} w-5" aria-hidden="true"></i>
                    <span>{{ $label }}</span>
                </a>
            @endforeach
            <form method="POST" action="{{ route('logout') }}" class="pt-2">
                @csrf
                <button type="submit" class="flex w-full items-center gap-3 px-4 py-3 rounded-lg text-gray-600 transition hover:bg-red-50 hover:text-red-600">
                    <i class="fas fa-sign-out-alt w-5" aria-hidden="true"></i><span>Logout</span>
                </button>
            </form>
        </nav>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col min-h-screen min-w-0">
        <header class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-30">
            <div class="flex items-center justify-between h-16 px-4 md:px-6">
                <button type="button" @click="sidebarOpen = true" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:bg-gray-100" aria-label="Open menu">
                    <i class="fas fa-bars text-lg" aria-hidden="true"></i>
                </button>
                <div class="md:hidden font-semibold text-lg text-gray-800 truncate px-2">@yield('title', 'Dashboard')</div>
                <div class="hidden md:block text-sm text-gray-500">{{ $isTeacher ? 'Teacher' : 'Student' }} area</div>

                <div x-data="{ open: false }" class="relative">
                    <button type="button" @click="open = !open" @keydown.escape="open = false" class="flex items-center gap-3 rounded-full p-1 text-gray-600 hover:text-primary-600" :aria-expanded="open.toString()" aria-haspopup="true">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-100 text-primary-600"><i class="fas fa-user" aria-hidden="true"></i></span>
                        <span class="hidden md:block text-sm font-medium text-gray-700">{{ $user->name }}</span>
                        <i class="hidden md:block fas fa-chevron-down text-xs text-gray-400" aria-hidden="true"></i>
                    </button>
                    <div x-show="open" x-cloak x-transition @click.outside="open = false" class="absolute right-0 z-10 mt-3 w-56 origin-top-right overflow-hidden rounded-lg border border-gray-200 bg-white shadow-lg">
                        <div class="py-1">
                            @if ($isTeacher)
                                <a href="{{ route('teach.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 no-underline hover:bg-gray-100 hover:text-primary-600"><i class="fas fa-user fa-fw mr-2" aria-hidden="true"></i> My profile</a>
                            @endif
                            <a href="{{ route('account.subscription') }}" class="block px-4 py-2 text-sm text-gray-700 no-underline hover:bg-gray-100 hover:text-primary-600"><i class="fas fa-crown fa-fw mr-2" aria-hidden="true"></i> My subscription</a>
                            <a href="{{ route('password.change') }}" class="block px-4 py-2 text-sm text-gray-700 no-underline hover:bg-gray-100 hover:text-primary-600"><i class="fas fa-key fa-fw mr-2" aria-hidden="true"></i> Change password</a>
                            <hr class="my-1 border-gray-200">
                            <form method="POST" action="{{ route('logout') }}">@csrf
                                <button type="submit" class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 hover:text-primary-600"><i class="fas fa-sign-out-alt fa-fw mr-2" aria-hidden="true"></i> Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main id="main" class="flex-1 flex flex-col min-w-0 px-4 md:px-6 py-4">
            <x-flash />
            @unless (View::hasSection('hide_heading'))
                <div class="py-2 mt-1 mb-2 flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h1 class="text-2xl font-bold text-gray-800">@yield('title', 'Dashboard')</h1>
                        @hasSection('subtitle')<p class="text-sm text-gray-500">@yield('subtitle')</p>@endif
                    </div>
                    @hasSection('actions')<div class="flex flex-wrap gap-2">@yield('actions')</div>@endif
                </div>
            @endunless
            @yield('content')
        </main>

        <footer class="py-4 px-6 border-t border-gray-200">
            <div class="flex flex-col md:flex-row justify-between items-center gap-2">
                <p class="text-sm text-gray-500">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                <div class="text-sm">
                    <a href="{{ route('privacy') }}" class="text-gray-500 hover:text-primary-600 mr-4">Privacy Policy</a>
                    <a href="{{ route('terms') }}" class="text-gray-500 hover:text-primary-600">Terms of Service</a>
                </div>
            </div>
        </footer>
    </div>
</div>
    @stack('scripts')
</body>
</html>
