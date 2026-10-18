<?php

namespace Tests\Feature;

use App\Models\Curriculum\Subject;
use App\Models\User;
use App\Models\Videos\VideoLesson;
use App\Services\Videos\YoutubeChannelFeed;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class YoutubeImportTest extends TestCase
{
    private const CHANNEL = 'UCoXMXPxUrIJyjPAQp489Byg';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.youtube.key' => null]);
        foreach (['core-mathematics' => 'Core Mathematics', 'elective-mathematics' => 'Elective Mathematics'] as $slug => $name) {
            Subject::firstOrCreate(['slug' => $slug], ['name' => $name]);
        }
    }

    private function rss(): string
    {
        $entry = fn ($id, $title, $date) => "<entry><yt:videoId>{$id}</yt:videoId><title>{$title}</title><published>{$date}</published><media:group><media:description>About {$title}</media:description></media:group></entry>";

        return '<?xml version="1.0"?><feed xmlns:yt="http://www.youtube.com/xml/schemas/2015" xmlns:media="http://search.yahoo.com/mrss/" xmlns="http://www.w3.org/2005/Atom">'
            .$entry('aaaaaaaaaa1', 'The Quotient rule', '2026-10-10T10:00:00+00:00')
            .$entry('bbbbbbbbbb2', 'SAT EPISODE 2', '2026-10-09T10:00:00+00:00')
            .$entry('cccccccccc3', 'Multiplication of matrices in simple terms', '2026-10-08T10:00:00+00:00')
            .'</feed>';
    }

    public function test_rss_import_publishes_new_uploads_and_files_them_by_subject(): void
    {
        Http::fake(['www.youtube.com/feeds/*' => Http::response($this->rss())]);

        $this->artisan('videos:import-youtube')->assertSuccessful();

        $this->assertSame(3, VideoLesson::count());
        $quotient = VideoLesson::where('youtube_id', 'aaaaaaaaaa1')->firstOrFail();
        $this->assertSame('published', $quotient->status);
        $this->assertSame('elective-mathematics', $quotient->subject->slug);
        $this->assertSame('Otisam Mathematics', $quotient->instructor);
        $this->assertSame('About The Quotient rule', $quotient->description);
        $this->assertSame('2026-10-10', $quotient->published_at->toDateString());

        $sat = VideoLesson::where('youtube_id', 'bbbbbbbbbb2')->firstOrFail();
        $this->assertSame('core-mathematics', $sat->subject->slug);
        $this->assertSame('Sat Episode 2', $sat->title, 'All-caps titles are tidied.');
        $this->assertSame('elective-mathematics', VideoLesson::where('youtube_id', 'cccccccccc3')->firstOrFail()->subject->slug);

        $this->get(route('videos.index'))->assertOk()->assertSee('The Quotient rule');
    }

    public function test_reimport_never_overwrites_or_restores_admin_changes(): void
    {
        Http::fake(['www.youtube.com/feeds/*' => Http::response($this->rss())]);
        $this->artisan('videos:import-youtube')->assertSuccessful();

        VideoLesson::where('youtube_id', 'aaaaaaaaaa1')->first()->update(['title' => 'Quotient rule (edited)']);
        VideoLesson::where('youtube_id', 'bbbbbbbbbb2')->first()->delete();

        $this->artisan('videos:import-youtube')->expectsOutputToContain('0 new, 3 already')->assertSuccessful();

        $this->assertSame(2, VideoLesson::count());
        $this->assertSame('Quotient rule (edited)', VideoLesson::where('youtube_id', 'aaaaaaaaaa1')->value('title'));
    }

    public function test_api_import_pages_through_every_upload_with_durations(): void
    {
        config(['services.youtube.key' => 'test-key']);
        $item = fn ($id, $title) => ['contentDetails' => ['videoId' => $id, 'videoPublishedAt' => '2026-01-01T00:00:00Z'], 'snippet' => ['title' => $title, 'description' => ''], 'status' => ['privacyStatus' => 'public']];

        Http::fake([
            'www.googleapis.com/youtube/v3/playlistItems*' => Http::sequence()
                ->push(['items' => [$item('dddddddddd4', 'Limit at infinity')], 'nextPageToken' => 'P2'])
                ->push(['items' => [$item('eeeeeeeeee5', 'Fractions'), ['contentDetails' => ['videoId' => 'ffffffffff6'], 'snippet' => ['title' => 'Hidden'], 'status' => ['privacyStatus' => 'private']]]]),
            'www.googleapis.com/youtube/v3/videos*' => Http::sequence()
                ->push(['items' => [['id' => 'dddddddddd4', 'contentDetails' => ['duration' => 'PT12M30S']]]])
                ->push(['items' => [['id' => 'eeeeeeeeee5', 'contentDetails' => ['duration' => 'PT1H2M']]]]),
        ]);

        $this->artisan('videos:import-youtube', ['channel' => 'otisam-maths'])->assertSuccessful();

        $this->assertEqualsCanonicalizing(['dddddddddd4', 'eeeeeeeeee5'], VideoLesson::pluck('youtube_id')->all());
        $this->assertSame(13, VideoLesson::where('youtube_id', 'dddddddddd4')->value('duration_minutes'));
        $this->assertSame(62, VideoLesson::where('youtube_id', 'eeeeeeeeee5')->value('duration_minutes'));
        Http::assertSent(fn ($request) => str_contains($request->url(), 'playlistId=UU'.substr(self::CHANNEL, 2)));
    }

    public function test_iso_durations(): void
    {
        $this->assertSame(3723, YoutubeChannelFeed::isoDurationToSeconds('PT1H2M3S'));
        $this->assertSame(45, YoutubeChannelFeed::isoDurationToSeconds('PT45S'));
        $this->assertNull(YoutubeChannelFeed::isoDurationToSeconds('nonsense'));
    }

    public function test_admin_import_button_and_permission(): void
    {
        Http::fake(['www.youtube.com/feeds/*' => Http::response($this->rss())]);

        $finance = User::factory()->staff('Finance Officer')->create();
        $this->actingAs($finance)->post(route('admin.videos.import-youtube'))->assertForbidden();

        $admin = User::factory()->staff('Administrator')->create();
        $this->actingAs($admin)->get(route('admin.videos.index'))->assertOk()->assertSee('Import from YouTube');
        $this->post(route('admin.videos.import-youtube'))->assertSessionHas('success');
        $this->assertSame(3, VideoLesson::count());
    }

    public function test_feed_failure_is_reported_not_thrown(): void
    {
        Http::fake(['www.youtube.com/feeds/*' => Http::response('', 500)]);

        $this->artisan('videos:import-youtube')->assertFailed();
        $admin = User::factory()->staff('Administrator')->create();
        $this->actingAs($admin)->post(route('admin.videos.import-youtube'))->assertSessionHas('error');
    }
}
