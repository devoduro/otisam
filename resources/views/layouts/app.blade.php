<!DOCTYPE html>
{{-- Public pages. Styling follows jhs_portal; the phone bottom tab bar is borrowed from examsghana.com. --}}
@php
    $links = [
        'home' => ['Home', route('home'), 'fa-house', request()->routeIs('home')],
        'library' => ['Library', route('library.index'), 'fa-book-open', request()->routeIs('library.*')],
        'practice' => ['Practice', route('practice.index'), 'fa-list-check', request()->routeIs('practice.*')],
        'videos' => ['Video lessons', route('videos.index'), 'fa-circle-play', request()->routeIs('videos.*')],
        'lesson-plans' => ['Lesson plans', route('lesson-plans.index'), 'fa-clipboard-list', request()->routeIs('lesson-plans.*')],
        'subject-apps' => ['Subject apps', route('subject-apps.index'), 'fa-robot', request()->routeIs('subject-apps.*')],
        'qa' => ['Q&A', route('qa.index'), 'fa-comments', request()->routeIs('qa.*')],
        'downloads' => ['Downloads', route('downloads.index'), 'fa-download', request()->routeIs('downloads.*')],
        'teachers' => ['Teachers', route('teachers.index'), 'fa-user-tie', request()->routeIs('teachers.index', 'teachers.show')],
        'promotion' => ['Promotion', route('promotion'), 'fa-chalkboard-user', request()->routeIs('promotion')],
        'subjects' => ['Subjects', route('subjects.index'), 'fa-sitemap', request()->routeIs('subjects.*')],
        'premium' => ['Premium', route('plans.index'), 'fa-crown', request()->routeIs('plans.*')],
    ];
    $navLinks = array_values($links);
    // The wide-screen bar is full: these stay in the menu, footer and teacher sidebar only.
    $barLinks = array_values(\Illuminate\Support\Arr::except($links, ['lesson-plans', 'subject-apps', 'qa']));
    $accountUrl = auth()->check() ? route(auth()->user()->homeRoute()) : route('login');
    // Phone tab bar: the five most used destinations (examsghana-style).
    $tabLinks = [
        // Short labels for the narrow tab bar; [label] + link keeps the link's url, icon and active state.
        ['Home'] + $links['home'],
        ['Library'] + $links['library'],
        ['Videos'] + $links['videos'],
        ['Downloads'] + $links['downloads'],
        [auth()->check() ? 'Me' : 'Sign in', $accountUrl, 'fa-user', request()->routeIs('login', 'learn.*', 'teach.*')],
    ];
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
<body class="min-h-screen flex flex-col bg-canvas font-sans antialiased pb-16 md:pb-0">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-2 focus:top-2 focus:z-[60] focus:rounded focus:bg-white focus:px-3 focus:py-2">Skip to content</a>

    <header class="sticky top-0 z-40 border-b border-gray-100 bg-white/95 shadow-sm backdrop-blur">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 md:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-2 no-underline">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl gradient-bg text-sm font-bold text-white">{{ mb_substr(config('app.name'), 0, 1) }}</span>
                <span class="font-display text-xl font-extrabold text-primary-700">{{ config('app.name') }}</span>
            </a>

            <nav aria-label="Main" class="hidden xl:flex items-center gap-0.5 text-sm">
                @foreach ($barLinks as [$label, $href, $icon, $active])
                    <a href="{{ $href }}" @if ($active) aria-current="page" @endif
                       class="flex items-center gap-1.5 whitespace-nowrap rounded-xl px-2 py-2 2xl:px-2.5 no-underline transition {{ $active ? 'bg-gold-50 text-gold-600 font-semibold' : 'font-medium text-primary-700 hover:bg-gold-50 hover:text-gold-600' }}">
                        <i class="fas {{ $icon }} hidden 2xl:inline" aria-hidden="true"></i>{{ $label }}
                    </a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2 text-sm">
                {{-- Menu button below xl: the full list of destinations --}}
                <div x-data="{ open: false }" class="relative xl:hidden" @keydown.escape="open = false">
                    <button type="button" @click="open = !open" :aria-expanded="open.toString()" aria-controls="site-menu"
                            class="flex items-center gap-2 rounded-md px-3 py-2 font-medium text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-bars" aria-hidden="true"></i><span class="hidden sm:inline">Menu</span>
                    </button>
                    <div id="site-menu" x-show="open" x-cloak x-transition @click.outside="open = false"
                         class="absolute right-0 z-50 mt-2 w-60 overflow-hidden rounded-lg border border-gray-200 bg-white py-1 shadow-lg">
                        @foreach ($navLinks as [$label, $href, $icon, $active])
                            <a href="{{ $href }}" @if ($active) aria-current="page" @endif
                               class="flex items-center gap-3 px-4 py-2.5 no-underline {{ $active ? 'bg-gold-50 text-gold-600 font-semibold' : 'text-primary-700 hover:bg-gold-50 hover:text-gold-600' }}">
                                <i class="fas {{ $icon }} w-5 text-center" aria-hidden="true"></i>{{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @auth
                    <a href="{{ $accountUrl }}" class="hidden sm:inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 font-bold text-white no-underline shadow-navy hover:bg-primary-700 hover:text-white">
                        <i class="fas fa-tachometer-alt" aria-hidden="true"></i> Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button type="submit" class="rounded-md px-3 py-2 text-gray-600 hover:bg-gray-100" title="Logout"><i class="fas fa-sign-out-alt" aria-hidden="true"></i><span class="sr-only">Logout</span></button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-xl px-3 py-2 font-semibold text-primary-700 no-underline hover:bg-gray-100 hover:text-primary-800">Sign in</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 font-bold text-white no-underline shadow-navy hover:bg-primary-700 hover:text-white">Sign up free <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i></a>
                @endauth
            </div>
        </div>
    </header>

    <main id="main" class="flex-1">
        @yield('content')
    </main>

    <footer class="bg-primary-800 bg-crosshatch text-white/60">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 md:grid-cols-4 md:px-6">
            <div class="md:col-span-2">
                <a href="{{ route('home') }}" class="flex items-center gap-2 no-underline">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl gradient-bg text-sm font-bold text-white">{{ mb_substr(config('app.name'), 0, 1) }}</span>
                    <span class="font-display text-lg font-extrabold text-white">{{ config('app.name') }}</span>
                </a>
                <p class="mt-3 max-w-sm text-sm leading-relaxed text-white/60">Past questions, worked solutions and curriculum-mapped study material for JHS and SHS students and teachers in Ghana.</p>
            </div>
            <div>
                <p class="text-[13px] font-bold uppercase tracking-wider text-white">Learn</p>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><a href="{{ route('library.index') }}" class="text-white/60 no-underline hover:text-gold-400">Library</a></li>
                    <li><a href="{{ route('practice.index') }}" class="text-white/60 no-underline hover:text-gold-400">Practice questions</a></li>
                    <li><a href="{{ route('videos.index') }}" class="text-white/60 no-underline hover:text-gold-400">Video lessons</a></li>
                    <li><a href="{{ route('lesson-plans.index') }}" class="text-white/60 no-underline hover:text-gold-400">Lesson plans</a></li>
                    <li><a href="{{ route('subject-apps.index') }}" class="text-white/60 no-underline hover:text-gold-400">Subject apps</a></li>
                    <li><a href="{{ route('qa.index') }}" class="text-white/60 no-underline hover:text-gold-400">Q&amp;A</a></li>
                    <li><a href="{{ route('downloads.index') }}" class="text-white/60 no-underline hover:text-gold-400">Downloads</a></li>
                    <li><a href="{{ route('subjects.index') }}" class="text-white/60 no-underline hover:text-gold-400">Subjects &amp; topics</a></li>
                    <li><a href="{{ route('plans.index') }}" class="text-white/60 no-underline hover:text-gold-400">Premium plans</a></li>
                    <li><a href="{{ route('promotion') }}" class="text-white/60 no-underline hover:text-gold-400">Teacher promotion exams</a></li>
                    <li><a href="{{ route('teachers.index') }}" class="text-white/60 no-underline hover:text-gold-400">Our teachers</a></li>
                    <li><a href="{{ route('teachers.register') }}" class="text-white/60 no-underline hover:text-gold-400">For teachers</a></li>
                </ul>
            </div>
            <div>
                <p class="text-[13px] font-bold uppercase tracking-wider text-white">About</p>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><a href="{{ route('terms') }}" class="text-white/60 no-underline hover:text-gold-400">Terms of Service</a></li>
                    <li><a href="{{ route('privacy') }}" class="text-white/60 no-underline hover:text-gold-400">Privacy Policy</a></li>
                    <li><a href="{{ route('copyright') }}" class="text-white/60 no-underline hover:text-gold-400">Copyright &amp; removal</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10 py-5 px-6 text-center text-[13px] text-white/50">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</div>
    </footer>

    <!-- Phone tab bar (examsghana-style) -->
    <nav aria-label="Quick links" class="fixed inset-x-0 bottom-0 z-40 grid grid-cols-5 border-t border-gray-200 bg-white md:hidden">
        @foreach ($tabLinks as [$label, $href, $icon, $active])
            <a href="{{ $href }}" @if ($active) aria-current="page" @endif
               class="flex flex-col items-center justify-center gap-0.5 py-2 text-[11px] no-underline {{ $active ? 'text-gold-600 font-semibold' : 'text-primary-700/70' }}">
                <i class="fas {{ $icon }} text-lg" aria-hidden="true"></i>{{ $label }}
            </a>
        @endforeach
    </nav>
    @stack('scripts')
</body>
</html>
