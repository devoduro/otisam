@extends('layouts.portal')

@section('title', 'Student logins')
@section('subtitle', count($rows).' account(s). PINs are shown only here: print or download them now.')

@section('actions')
    <x-button :href="route('teach.class-list.download')" icon="fa-file-csv">Download CSV</x-button>
    <x-button variant="accent" :href="route('teach.class-list.credentials', ['print' => 1])" target="_blank" rel="noopener" icon="fa-print">Print slips</x-button>
@endsection

@section('content')
@if ($enrolled)
    <div class="mb-4 rounded-xl bg-primary-50 px-4 py-3 text-sm text-primary-800" role="status">
        <p class="font-semibold">Existing accounts added to a class ({{ count($enrolled) }}):</p>
        <ul class="mt-1 list-disc pl-5">@foreach ($enrolled as $line)<li>{{ $line }}</li>@endforeach</ul>
    </div>
@endif
@if ($skipped)
    <div class="mb-4 rounded-xl bg-gold-50 px-4 py-3 text-sm text-gray-700" role="status">
        <p class="font-semibold">Not added again ({{ count($skipped) }}):</p>
        <ul class="mt-1 list-disc pl-5">@foreach ($skipped as $line)<li>{{ $line }}</li>@endforeach</ul>
    </div>
@endif

<div class="panel overflow-x-auto p-0">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
            <tr><th class="px-4 py-3">Full name</th><th class="px-4 py-3">Class</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Username</th><th class="px-4 py-3">PIN</th></tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr class="border-t border-gray-100">
                    <td class="px-4 py-2.5 font-medium text-gray-800">{{ $row['name'] }}</td>
                    <td class="px-4 py-2.5 text-gray-600">{{ $row['class'] }}</td>
                    <td class="px-4 py-2.5 text-gray-600">{{ ($row['email'] ?? null) ?: '—' }}</td>
                    <td class="px-4 py-2.5 font-mono">{{ $row['username'] }}</td>
                    <td class="px-4 py-2.5 font-mono font-bold tracking-widest">{{ $row['pin'] }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">No new accounts were created.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<form method="POST" action="{{ route('teach.class-list.forget') }}" class="mt-4" onsubmit="return confirm('Have you printed or downloaded the logins? You cannot see these PINs again.')">
    @csrf @method('DELETE')
    <x-button variant="outline" icon="fa-eye-slash">I have saved them: clear this sheet</x-button>
</form>
@endsection
