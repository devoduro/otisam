{{-- One video card in a grid: expects $video. --}}
<li>
    <a href="{{ route('videos.show', $video) }}" class="group block h-full overflow-hidden rounded-2xl bg-white no-underline shadow-card ring-1 ring-[#ece9f3] transition hover:-translate-y-0.5 hover:shadow-md">
        <span class="relative block aspect-video bg-slate-900">
            <img src="{{ $video->thumbnailUrl() }}" alt="" loading="lazy" class="h-full w-full object-cover opacity-90">
            <span class="absolute inset-0 flex items-center justify-center"><span class="flex h-12 w-12 items-center justify-center rounded-full bg-white/90 text-primary-600 shadow"><i class="fas fa-play" aria-hidden="true"></i></span></span>
            @if ($video->duration_minutes)<span class="absolute bottom-2 right-2 rounded bg-black/70 px-1.5 py-0.5 text-[11px] font-semibold text-white">{{ $video->duration_minutes }} min</span>@endif
            @unless ($video->isFree())<span class="absolute left-2 top-2 rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-800"><i class="fas fa-crown mr-1" aria-hidden="true"></i>Premium</span>@endunless
        </span>
        <span class="block p-4">
            <span class="block font-semibold text-gray-800">{{ $video->title }}</span>
            <span class="mt-1 block text-xs text-gray-500">{{ collect([$video->subject?->name, $video->classLevel?->name, $video->instructor])->filter()->join(' · ') }}</span>
        </span>
    </a>
</li>
