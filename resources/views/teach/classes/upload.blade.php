@extends('layouts.portal')

@section('title', 'Upload class list')
@section('subtitle', 'Create student logins from a list of names')

@section('content')
@php $control = 'block w-full rounded-xl border-[1.5px] border-[#e2e0ea] bg-white px-3.5 py-3 text-[15px]'; @endphp
<div class="grid gap-6 lg:grid-cols-3">
    <form method="POST" action="{{ route('teach.class-list.store') }}" enctype="multipart/form-data" class="panel space-y-5 p-6 lg:col-span-2">
        @csrf

        <div>
            <label for="file" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">Class list file <span class="font-normal text-gray-500">(CSV)</span></label>
            <input id="file" type="file" name="file" accept=".csv,text/csv,text/plain" class="block w-full text-sm file:mr-3 file:rounded-xl file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:font-semibold file:text-primary-700">
            <p class="mt-1 text-xs text-gray-500">Three columns: <strong>Full name</strong>, <strong>Class</strong> and <strong>Email</strong> (email is optional but recommended). In Excel, use File → Save As → CSV. <a href="{{ route('teach.class-list.template') }}">Download a template</a>.</p>
            @error('file')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="list" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">Or paste the list</label>
            <textarea id="list" name="list" rows="10" maxlength="60000" class="{{ $control }} font-mono text-sm" placeholder="Ama Serwaa Owusu, JHS 2A, ama.owusu@example.com&#10;Kwame Boateng, JHS 2A, kwame.boateng@example.com&#10;Esi Mensah, JHS 2B">{{ old('list') }}</textarea>
            <p class="mt-1 text-xs text-gray-500">One student per line: full name, class name, then email. You can copy the columns straight from Excel or Google Sheets.</p>
            @error('list')
                @foreach ($errors->get('list') as $message)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@endforeach
            @enderror
        </div>

        <div>
            <label for="classroom" class="mb-1.5 block text-[13.5px] font-bold text-primary-800">Class for names with no class <span class="font-normal text-gray-500">(optional)</span></label>
            <select id="classroom" name="classroom" class="{{ $control }}">
                <option value="">Use the class name on each line</option>
                @foreach ($classrooms as $c)<option value="{{ $c->ulid }}" @selected(old('classroom', $selected) === $c->ulid)>{{ $c->name }}</option>@endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">A class name you have not used yet creates a new class.</p>
        </div>

        <x-button icon="fa-user-plus">Create student logins</x-button>
    </form>

    <aside class="panel space-y-3 p-5 text-sm text-gray-600">
        <h2 class="font-bold text-gray-800">How it works</h2>
        <p>Each student gets a <strong>username</strong> (like <span class="font-mono">ama.owusu482</span>) and a <strong>6-digit PIN</strong>. They sign in on the normal sign-in page.</p>
        <p><strong>Add their email</strong> to make the account their own for good: they can also sign in with the email, and they choose their own password the first time. If a student already has an account with that email, it is added to the class; no new login is made.</p>
        <p>You will see the logins once, straight after uploading. Print the slips or download the sheet before you leave the page.</p>
        <p>Students are added to the class automatically. Uploading the same names again does not make duplicates.</p>
        <p>If a student forgets their PIN, open the class and press <strong>New PIN</strong> next to their name.</p>
    </aside>
</div>
@endsection
