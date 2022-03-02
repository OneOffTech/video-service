<?php

namespace Tests\Feature\Http\Controllers\Management;

use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VideoControllerTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_video_can_be_uploaded()
    {
        Storage::fake('local');

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        
        $videoFile = UploadedFile::fake()->create('awesome-video.mp4', 15000, 'video/mp4');

        $response = $this
            ->actingAs($user)
            ->from('/videos/create')
            ->post('/videos', [
                'file' => $videoFile,
                'title' => 'A video',
                'description' => null,
                'language' => null,
                'license' => null,
            ]);

        $video = Video::first();

        $this->assertNotNull($video);

        $response->assertRedirect(route('videos.edit', [$video->uuid]));

        Storage::disk('local')->assertExists($video->file_name);


    }
}
