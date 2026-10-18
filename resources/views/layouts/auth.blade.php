@extends('layouts.app')

@section('content')
<div class="mx-auto w-full max-w-md px-4 py-8 sm:py-12">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-2xl font-extrabold text-primary-900">@yield('heading')</h1>
        @hasSection('intro')
            <p class="mt-1 text-muted">@yield('intro')</p>
        @endif
        <div class="mt-6">
            <x-flash />
            @yield('form')
        </div>
    </div>
    @yield('after')
</div>
@endsection
