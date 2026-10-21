<?php

namespace Tests\Feature;

use App\Support\SubjectApps;
use Tests\TestCase;

class SubjectAppsTest extends TestCase
{
    public function test_every_app_link_is_a_playlab_project(): void
    {
        $apps = SubjectApps::all();
        $this->assertCount(5 + 43 + 43 + 44, $apps);
        foreach ($apps as $app) {
            $this->assertMatchesRegularExpression('#^https://www\.playlab\.ai/project/[a-z0-9]{25}$#', $app['url'], $app['subject']);
        }
    }

    public function test_directory_lists_apps_and_filters_by_level_year_and_name(): void
    {
        $chemistryY2 = SubjectApps::url(SubjectApps::SHS[2]['Chemistry']);

        $this->get(route('subject-apps.index'))->assertOk()
            ->assertSee('Subject Specific App')
            ->assertSee('JHS 1–3')->assertSee('SHS Year 3')
            ->assertSee($chemistryY2)
            ->assertSee('rel="noopener noreferrer"', false);

        $this->get(route('subject-apps.index', ['level' => 'shs', 'year' => 2]))->assertOk()
            ->assertSee($chemistryY2)
            ->assertDontSee(SubjectApps::url(SubjectApps::SHS[1]['Chemistry']))
            ->assertDontSee(SubjectApps::url(SubjectApps::JHS['Science']));

        $this->get(route('subject-apps.index', ['q' => 'chem']))->assertOk()
            ->assertSee($chemistryY2)->assertDontSee(SubjectApps::url(SubjectApps::SHS[2]['Physics']));

        $this->get(route('subject-apps.index', ['level' => 'nope']))->assertSessionHasErrors('level');
    }

    public function test_teachers_page_links_to_subject_apps(): void
    {
        $this->get(route('teachers.index'))->assertOk()->assertSee(route('subject-apps.index'));
    }
}
