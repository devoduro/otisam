@props([
    'title' => 'Welcome back',
    'subtitle' => 'Please enter your details',
    'pageTitle' => 'Sign in',
    'tagline' => 'Past questions, worked solutions and practice for SHS students in Ghana.',
    'wide' => false,
])
{{-- Split-screen sign-in layout from jhs_portal: form on the left, illustrated brand panel on the right. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }} — {{ config('app.name') }}</title>
    @include('partials.head-assets')
</head>
<body class="min-h-screen bg-white font-sans text-gray-900 antialiased">
<div class="flex min-h-screen">
    <!-- Form side -->
    <div class="flex w-full flex-col px-6 py-8 sm:px-10 lg:w-1/2 xl:px-16">
        <a href="{{ route('home') }}" class="flex items-center gap-3 text-gray-900 no-underline hover:text-gray-900">
            <span class="flex h-11 w-11 items-center justify-center rounded-lg gradient-bg text-lg font-bold text-white">{{ mb_substr(config('app.name'), 0, 1) }}</span>
            <span class="text-xl font-semibold tracking-tight">{{ config('app.name') }}</span>
        </a>

        <div class="flex flex-1 items-center justify-center py-10">
            <div class="w-full {{ $wide ? 'max-w-lg' : 'max-w-sm' }}">
                <h1 class="text-3xl font-extrabold text-primary-700 sm:text-4xl">{{ $title }}</h1>
                <p class="mt-2 text-gray-500">{{ $subtitle }}</p>

                <div class="mt-6">
                    <x-flash />
                    @if ($errors->any() && $errors->count() > 1)
                        <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                            Please check the highlighted fields.
                        </div>
                    @endif
                </div>

                <div class="mt-2">
                    {{ $slot }}
                </div>

                @isset($footer)
                    <div class="mt-8 text-center text-sm text-gray-500">{{ $footer }}</div>
                @endisset
            </div>
        </div>

        <p class="text-xs text-gray-400">&copy; {{ date('Y') }} {{ config('app.name') }}. <a href="{{ route('terms') }}" class="text-gray-400">Terms</a> · <a href="{{ route('privacy') }}" class="text-gray-400">Privacy</a></p>
    </div>

    <!-- Illustration side -->
    <div class="relative hidden overflow-hidden bg-brand-700 bg-crosshatch lg:sticky lg:top-0 lg:flex lg:h-screen lg:w-1/2 lg:flex-col lg:items-center lg:justify-center">
        <svg class="w-full max-w-xl px-10" viewBox="0 0 600 560" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <g stroke="#8fa0e3" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" opacity=".55">
                <path d="M70 60l12 12M82 60L70 72M520 70l12 12M532 70l-12 12M470 200l10 10M480 200l-10 10M120 260l10 10M130 260l-10 10M300 30l10 10M310 30l-10 10"/>
                <circle cx="40" cy="160" r="7"/><circle cx="560" cy="150" r="7"/><circle cx="430" cy="40" r="6"/><circle cx="200" cy="40" r="6"/><circle cx="560" cy="300" r="6"/>
                <path d="M240 70h22M360 60h22M150 120h18M500 250h22M60 330h18M380 250h16"/>
                <path d="M60 90h90a10 10 0 0110 10v50a10 10 0 01-10 10H95l-20 20v-20H60a10 10 0 01-10-10v-50a10 10 0 0110-10z"/>
                <path d="M105 112v3M105 122v22"/>
                <path d="M470 110a40 32 0 1180 0a40 32 0 01-40 32l-18 14v-17a40 32 0 01-22-29z"/>
                <circle cx="495" cy="110" r="4"/><circle cx="510" cy="110" r="4"/><circle cx="525" cy="110" r="4"/>
                <path d="M180 200c25-12 55-12 80 0v62c-25-12-55-12-80 0zM260 200c25-12 55-12 80 0v62c-25-12-55-12-80 0z"/>
                <path d="M200 215h40M200 228h40M280 215h40M280 228h40"/>
                <rect x="380" y="170" width="70" height="90" rx="8"/>
                <path d="M395 190h40M395 210h10M415 210h10M395 228h10M415 228h10M395 246h40"/>
                <rect x="60" y="380" width="96" height="66" rx="4"/><path d="M60 384l48 36 48-36"/>
                <path d="M470 360h60v86l-10-7-10 7-10-7-10 7-10-7-10 7z"/><path d="M485 380h30M485 395h30M485 410h18"/>
            </g>
            <g transform="translate(225 300)">
                <path d="M75 0L0 32l75 32 75-32z" fill="#10163f"/>
                <path d="M30 46v32c0 14 20 26 45 26s45-12 45-26V46l-45 18z" fill="#16204f"/>
                <path d="M140 36v42" stroke="#10163f" stroke-width="4" stroke-linecap="round"/>
                <circle cx="140" cy="84" r="7" fill="#ffaf1b"/>
            </g>
            <g transform="translate(120 450)">
                <rect x="0" y="0" width="150" height="70" rx="35" stroke="#dde3f7" stroke-width="3" fill="none"/>
                <circle cx="35" cy="35" r="27" fill="#fff"/>
                <path d="M22 35l9 9 18-20" stroke="#28409e" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
            </g>
            <g transform="translate(300 410)">
                <rect x="0" y="0" width="190" height="120" rx="12" fill="#080b22"/>
                <rect x="12" y="12" width="166" height="96" rx="6" fill="#fff"/>
                <rect x="24" y="24" width="70" height="8" rx="4" fill="#ffaf1b"/>
                <rect x="24" y="42" width="142" height="5" rx="2.5" fill="#dde3f7"/>
                <rect x="24" y="54" width="142" height="5" rx="2.5" fill="#dde3f7"/>
                <rect x="24" y="66" width="100" height="5" rx="2.5" fill="#dde3f7"/>
                <rect x="24" y="84" width="30" height="14" rx="3" fill="#0f9d58"/>
                <rect x="60" y="78" width="30" height="20" rx="3" fill="#3a52b8"/>
                <rect x="96" y="72" width="30" height="26" rx="3" fill="#ffaf1b"/>
                <rect x="132" y="66" width="30" height="32" rx="3" fill="#28409e"/>
                <path d="M80 120h30l6 22H74z" fill="#8fa0e3"/>
            </g>
            <path d="M30 520c15-15 25 15 40 0s25 15 40 0" stroke="#fff" stroke-width="2.5" stroke-linecap="round" opacity=".8"/>
            <path d="M500 330c15-15 25 15 40 0s25 15 40 0" stroke="#fff" stroke-width="2.5" stroke-linecap="round" opacity=".8"/>
        </svg>

        <div class="mt-6 max-w-md px-10 text-center text-white">
            <p class="text-2xl font-bold">{{ config('app.name') }}</p>
            <p class="mt-2 text-brand-50/90">{{ $tagline }}</p>
        </div>
    </div>
</div>
</body>
</html>
