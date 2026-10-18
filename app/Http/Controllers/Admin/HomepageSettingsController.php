<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Audit;
use App\Services\Library\UploadInspector;
use App\Services\Library\UploadRejected;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** Homepage look: the hero background picture (permission settings.manage). */
class HomepageSettingsController extends Controller
{
    public const DEFAULT_HERO = 'images/hero-default.svg';

    /** Public URL of the hero picture: the uploaded one if it still exists, else the bundled scene. */
    public static function heroImageUrl(): string
    {
        $path = Setting::get('home.hero_image');

        return asset($path && is_file(public_path($path)) ? $path : self::DEFAULT_HERO);
    }

    public function edit(): View
    {
        return view('admin.settings.homepage', [
            'heroUrl' => self::heroImageUrl(),
            'custom' => (bool) Setting::get('home.hero_image'),
        ]);
    }

    public function update(Request $request, UploadInspector $inspector): RedirectResponse
    {
        $request->validate(['hero_image' => ['required', 'file', 'max:5120']]);

        try {
            $upload = $inspector->inspect($request->file('hero_image'));
        } catch (UploadRejected $e) {
            throw ValidationException::withMessages(['hero_image' => $e->getMessage()]);
        }
        if (! in_array($upload->extension, ['jpg', 'png', 'webp'], true)) {
            throw ValidationException::withMessages(['hero_image' => 'Upload a photo (JPG, PNG or WebP).']);
        }

        // Public image, so it lives under public/ (works on cPanel without a storage symlink).
        $dir = public_path('uploads/homepage');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $relative = 'uploads/homepage/hero-'.Str::lower((string) Str::ulid()).'.'.$upload->extension;
        copy($upload->path, public_path($relative));

        $this->removeCurrent();
        Setting::put('home.hero_image', $relative, 'homepage');
        Cache::forget('home.v1');
        Audit::record('settings', 'homepage.hero', 'Homepage background picture changed');

        return back()->with('success', 'Background picture updated.');
    }

    public function destroy(): RedirectResponse
    {
        $this->removeCurrent();
        Setting::put('home.hero_image', null, 'homepage');
        Audit::record('settings', 'homepage.hero', 'Homepage background picture reset to the default');

        return back()->with('success', 'Background reset to the default picture.');
    }

    private function removeCurrent(): void
    {
        $current = Setting::get('home.hero_image');
        if ($current && str_starts_with($current, 'uploads/homepage/') && is_file(public_path($current))) {
            @unlink(public_path($current));
        }
    }
}
