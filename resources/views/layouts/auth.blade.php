{{--
    Sign-in, sign-up, verification and password pages: jhs_portal's split-screen layout.
    Child views fill the sections: heading, intro, form, after (and optionally `wide`).
--}}
<x-auth-split
    :title="trim($__env->yieldContent('heading'))"
    :subtitle="trim($__env->yieldContent('intro'))"
    :page-title="trim($__env->yieldContent('title')) ?: trim($__env->yieldContent('heading'))"
    :wide="$__env->hasSection('wide')"
>
    @yield('form')

    @hasSection('after')
        <x-slot:footer>@yield('after')</x-slot:footer>
    @endif
</x-auth-split>
