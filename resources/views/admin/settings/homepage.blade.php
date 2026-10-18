@extends('layouts.admin')

@section('title', 'Homepage')
@section('subtitle', 'The background picture behind the homepage banner')

@section('content')
<div class="row g-3">
    <div class="col-xl-7">
        <div class="card-panel">
            <h2 class="h6 fw-bold mb-2">Current background {{ $custom ? '' : '(default classroom scene)' }}</h2>
            <div class="position-relative rounded overflow-hidden" style="aspect-ratio: 16/6; background:#0f172a">
                <img src="{{ $heroUrl }}" alt="" class="position-absolute w-100 h-100" style="object-fit:cover; inset:0">
                <div class="position-absolute w-100 h-100" style="inset:0; background:linear-gradient(135deg, rgb(15 23 42 / .9), rgb(15 23 42 / .8), rgb(30 41 59 / .7))"></div>
                <div class="position-absolute w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white text-center p-3" style="inset:0">
                    <strong class="fs-4">Learn it. Practise it. Pass it.</strong>
                    <small class="opacity-75">Preview with the dark overlay the homepage uses</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <form method="POST" action="{{ route('admin.settings.homepage.update') }}" enctype="multipart/form-data" class="card-panel mb-3">
            @csrf @method('PUT')
            <h2 class="h6 fw-bold mb-2">Upload a new picture</h2>
            <label for="hero_image" class="form-label small">JPG, PNG or WebP, up to 5 MB. A wide photo (about 1920 × 700) works best.</label>
            <input id="hero_image" type="file" name="hero_image" accept=".jpg,.jpeg,.png,.webp" class="form-control mb-3" required>
            <p class="small text-muted">Use a photo you own or may use. It is re-saved to remove hidden data, and shown darkened so the text stays readable.</p>
            <button class="btn btn-premium" type="submit">Upload</button>
        </form>
        @if ($custom)
            <form method="POST" action="{{ route('admin.settings.homepage.destroy') }}" class="card-panel">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-secondary" type="submit" data-confirm="Go back to the default picture?">Use the default picture</button>
            </form>
        @endif
    </div>
</div>
@endsection
