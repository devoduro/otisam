@extends('layouts.app')

@section('content')
<section class="bg-primary-900 text-white">
    <div class="mx-auto max-w-6xl px-4 py-14 sm:py-20">
        <p class="text-sm font-bold uppercase tracking-wider text-accent-400">For SHS students and teachers in Ghana</p>
        <h1 class="mt-3 max-w-2xl text-3xl font-extrabold leading-tight sm:text-5xl">Learn it. Practise it. Pass it.</h1>
        <p class="mt-4 max-w-xl text-lg text-primary-100">Lessons mapped to the GES/NaCCA curriculum, WASSCE-style practice questions with worked solutions, and timed mock exams that work on any phone.</p>
        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
            @auth
                <x-button variant="accent" :href="route(auth()->user()->homeRoute())">Go to my dashboard</x-button>
            @else
                <x-button variant="accent" :href="route('register')">Create a free student account</x-button>
                <x-button variant="outline" :href="route('teachers.register')" class="border-white text-white hover:bg-primary-600">I'm a teacher</x-button>
            @endauth
        </div>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-12">
    <h2 class="text-xl font-extrabold text-primary-900">What's coming to the platform</h2>
    <p class="mt-1 text-muted">We are loading content now. Sign up to be ready when each part opens.</p>
    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        @foreach ([
            ['Practice by topic', 'Answer questions on each topic, see if you are right straight away, and read the explanation.'],
            ['Worked solutions', 'Step-by-step solutions and marking guides written by experienced teachers.'],
            ['Timed mock exams', 'Sit full papers against the clock and see which topics need more work.'],
        ] as [$title, $body])
            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <h3 class="font-bold text-primary-900">{{ $title }}</h3>
                <p class="mt-1 text-sm text-muted">{{ $body }}</p>
            </div>
        @endforeach
    </div>
</section>
@endsection
