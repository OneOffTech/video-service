<?php

namespace Tests\Feature\Oembed;

use Tests\TestCase;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OembedControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_oembed_api_returns_embed_code()
    {
        $video = Video::factory()->create();

        $expectedOembedRoute = route('videos.embed', $video);

        $response = $this->get('/api/oembed?format=json&url=' . route('videos.show', $video));

        $response->assertStatus(200);

        // Assert embed code follows specification
        $response->assertJson([
            "version" => "1.0",
            "type" => "rich",
            "provider_name" => config('app.name'),
            "provider_url" => config('app.url'),
            "width" => "960",
            "height" => "540",
            "title" => e($video->title),
            "html" => '<iframe width="960" height="540" src="'.$expectedOembedRoute.'" class="ootvs-embed" frameborder="0" allowfullscreen></iframe>'
        ]);

        // assert rate limit middleware applied
        $response->assertHeader('X-RateLimit-Limit', '60');
    }
}
