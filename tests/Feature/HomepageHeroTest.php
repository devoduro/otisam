<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class HomepageHeroTest extends TestCase
{
    public function test_homepage_shows_the_default_hero_background(): void
    {
        $this->get('/')->assertOk()->assertSee('images/hero-default.svg');
    }

    public function test_admin_uploads_and_resets_the_hero_background(): void
    {
        $admin = User::factory()->staff('Administrator')->create();

        $this->actingAs($admin)->get(route('admin.settings.homepage'))->assertOk();

        $this->put(route('admin.settings.homepage'), ['hero_image' => UploadedFile::fake()->createWithContent('notes.pdf', "%PDF-1.4\n%fake")])
            ->assertSessionHasErrors('hero_image');

        $this->put(route('admin.settings.homepage'), ['hero_image' => UploadedFile::fake()->image('class.jpg', 1600, 900)])
            ->assertSessionHasNoErrors();

        $path = Setting::get('home.hero_image');
        $this->assertStringStartsWith('uploads/homepage/hero-', $path);
        $this->assertFileExists(public_path($path));
        $this->get('/')->assertSee($path);

        $this->delete(route('admin.settings.homepage'))->assertSessionHasNoErrors();
        $this->assertNull(Setting::get('home.hero_image'));
        $this->assertFileDoesNotExist(public_path($path));
        $this->get('/')->assertSee('images/hero-default.svg');
    }

    public function test_hero_settings_need_the_settings_permission(): void
    {
        $finance = User::factory()->staff('Finance Officer')->create();

        $this->actingAs($finance)->get(route('admin.settings.homepage'))->assertForbidden();
        $this->put(route('admin.settings.homepage'), ['hero_image' => UploadedFile::fake()->image('x.jpg')])->assertForbidden();
    }
}
