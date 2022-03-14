<?php

namespace Tests\Feature\Jobs;

use App\Enums\ConversionTargets;
use App\Jobs\ConvertVideo;
use App\Jobs\GenerateThumbnailForVideo;
use App\Models\Blob;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ConvertVideoTest extends TestCase
{

    public function test_video_can_be_converted_to_hls()
    {
        Storage::fake('local');
        Storage::fake('public');

        $blob = Blob::factory()->video()->create();

        $expectedHlsPlaylist = "{$blob->disk_folder}/hls/{$blob->uuid}.m3u8";

        $job = new ConvertVideo($blob->video);

        $job->handle();

        $afterConversionBlob = $blob->fresh();

        $conversions = $afterConversionBlob->conversions;

        $this->assertEquals(
            [[
                'type' => ConversionTargets::HLS->value,
                'file_name' => $expectedHlsPlaylist,
            ]],
            $conversions->toArray()
        );


        Storage::disk('public')->assertExists($conversions[0]['file_name']);

    }
}
