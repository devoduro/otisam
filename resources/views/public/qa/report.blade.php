{{-- Report a Q&A post. Expects $action, $id (unique per page), $reasons. --}}
<details class="relative inline-block text-left">
    <summary class="cursor-pointer list-none text-xs font-semibold text-gray-400 hover:text-red-600"><i class="far fa-flag mr-1" aria-hidden="true"></i>Report</summary>
    <form method="POST" action="{{ $action }}" class="absolute right-0 z-10 mt-2 w-72 space-y-2 rounded-xl bg-white p-4 text-sm shadow-lg ring-1 ring-[#ece9f3]">
        @csrf
        <fieldset>
            <legend class="font-bold text-primary-800">What is wrong with it?</legend>
            @foreach ($reasons as $key => $label)
                <label class="mt-1 flex items-center gap-2"><input type="radio" name="reason" value="{{ $key }}" required> {{ $label }}</label>
            @endforeach
        </fieldset>
        <label for="note-{{ $id }}" class="block font-semibold text-gray-700">More detail <span class="font-normal text-gray-500">(optional)</span></label>
        <textarea id="note-{{ $id }}" name="note" rows="2" maxlength="300" class="block w-full rounded-lg border border-gray-300 p-2 text-sm"></textarea>
        <x-button variant="danger" class="w-full">Send report</x-button>
    </form>
</details>
