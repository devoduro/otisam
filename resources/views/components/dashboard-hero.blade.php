@props([
    'title',
    'eyebrow' => null,     // small line above the title (usually the date)
    'subtitle' => null,
])
{{-- Dark navy hero shared by every dashboard. Named slot "chips" = pills under the subtitle; default slot = action buttons. --}}
<div {{ $attributes->merge(['class' => 'relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary-800 via-primary-700 to-primary-600 p-7 text-white shadow-lift']) }}>
    <div class="pointer-events-none absolute -right-16 -top-20 h-64 w-64 rounded-full bg-gold-500/15 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-24 left-1/3 h-64 w-64 rounded-full bg-primary-400/20 blur-3xl"></div>
    <div class="pointer-events-none absolute inset-0 bg-crosshatch"></div>

    <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
        <div class="min-w-0">
            @if($eyebrow)<p class="text-sm font-medium text-gold-400">{{ $eyebrow }}</p>@endif
            <h2 class="mt-1 text-3xl font-extrabold">{{ $title }}</h2>
            @if($subtitle)<p class="mt-1 max-w-xl text-sm text-primary-100">{{ $subtitle }}</p>@endif

            @isset($chips)
                <div class="mt-4 flex flex-wrap gap-2 text-xs font-medium [&>span]:inline-flex [&>span]:items-center [&>span]:gap-1.5 [&>span]:rounded-full [&>span]:bg-white/10 [&>span]:px-3 [&>span]:py-1 [&>span]:ring-1 [&>span]:ring-white/15">
                    {{ $chips }}
                </div>
            @endisset
        </div>

        @if(!$slot->isEmpty())
            <div class="flex flex-wrap gap-3 [&>a]:inline-flex [&>a]:items-center [&>a]:gap-2 [&>a]:rounded-xl [&>a]:no-underline [&>a]:px-4 [&>a]:py-2.5 [&>a]:text-sm [&>a]:font-semibold [&>a]:transition [&>a]:bg-white/10 [&>a]:text-white [&>a]:ring-1 [&>a]:ring-white/20 hover:[&>a]:bg-white/20 [&>a.primary]:bg-gold-500 [&>a.primary]:text-primary-950 [&>a.primary]:font-bold [&>a.primary]:shadow-gold [&>a.primary]:ring-0 hover:[&>a.primary]:bg-gold-400">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>
