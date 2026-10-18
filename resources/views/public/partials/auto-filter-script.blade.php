@once
    @push('scripts')
        <script>
            // Filters apply as soon as a dropdown changes; broader choices clear the narrower ones.
            document.querySelectorAll('[data-catalogue-filters] select').forEach(function (select) {
                select.addEventListener('change', function () {
                    (select.dataset.resets || '').split(',').filter(Boolean).forEach(function (name) {
                        var field = select.form.elements[name];
                        if (field) { field.value = ''; }
                    });
                    select.form.requestSubmit();
                });
            });
        </script>
    @endpush
@endonce
