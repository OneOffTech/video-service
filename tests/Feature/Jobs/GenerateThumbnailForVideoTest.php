<?php

namespace Tests\Feature\Jobs;

use App\Jobs\GenerateThumbnailForVideo;
use App\Models\Blob;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GenerateThumbnailForVideoTest extends TestCase
{

    public function test_thumbnail_created()
    {
        Storage::fake('local');
        Storage::fake('public');

        $blob = Blob::factory()->video()->create();

        $video = $blob->video;

        $job = new GenerateThumbnailForVideo($video);

        $job->handle();

        $blobs = $video->blobs()->where('mime_type', 'image/jpeg')->get();

        $this->assertEquals(2, $blobs->count(), "No blob of type image/jpeg");

        foreach ($blobs as $thumbnailBlob) {
            Storage::disk('public')->assertExists($thumbnailBlob->file_name);
        }
    }
}
