@extends('layouts.app')

@section('title', 'Terms of use')

@section('content')
<article class="mx-auto max-w-3xl px-4 py-10">
    <h1 class="text-3xl font-extrabold text-gray-800">Terms of use</h1>
    <p class="mt-2 rounded-lg bg-amber-50 p-3 text-sm">Draft for legal review. These terms will be finalised before paid plans launch.</p>
    <div class="mt-6 space-y-4 text-gray-800">
        <p>By creating an account you agree to use {{ config('app.name') }} for your own learning or teaching, to keep your password private, and not to share your account.</p>
        <p>You must give accurate information when you register. Teacher accounts are checked by our administrators, and we may refuse or suspend an account that gives false details.</p>
        <p>Learning materials on the platform are for personal study. Do not copy, resell or republish them without permission.</p>
        <p>Practice tests and professional assessments on {{ config('app.name') }} are not official GES, WAEC or NaCCA examinations unless clearly stated with that body's authorisation.</p>
        <p>We may suspend accounts that misuse the platform. Contact support if you think a decision is wrong.</p>
    </div>
</article>
@endsection
