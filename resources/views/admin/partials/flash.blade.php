@foreach (['success' => 'success', 'status' => 'info', 'error' => 'danger', 'warning' => 'warning'] as $key => $type)
    @if (is_string(session($key)))
        <div class="alert alert-{{ $type }} alert-dismissible fade show" role="{{ $type === 'danger' ? 'alert' : 'status' }}">
            {{ session($key) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
@endforeach

@if (session('temporary_password'))
    <div class="alert alert-warning" role="alert">
        <div class="fw-bold mb-1">Temporary password (shown once)</div>
        <div class="temp-password mb-1">{{ session('temporary_password') }}</div>
        <div class="small">Give it to the staff member in person. They must change it the first time they sign in.</div>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <div class="fw-bold mb-1">Please fix the following:</div>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
