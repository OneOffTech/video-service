<?php

namespace Database\Factories;

use App\Enums\BlobRoles;
use App\Models\Blob;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\File;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blob>
 */
class BlobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'video_id' => Video::factory(),
            'disk' => 'local',
            'conversions_disk' => 'public',
        ];
    }

    
    /**
     * A video blob.
     *
     * Before creating a persisted model call Storage::fake('local') and Storage::fake('public')
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function video()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => $this->faker->title(),
                'file_name' => 'video/' . Str::random(20) . '/' . Str::random(20) . '.mp4',
                'mime_type' => 'video/mp4',
                'role' => BlobRoles::VIDEO,
                'size' => filesize(base_path('tests/src/video.mp4')),
            ];
        })
        ->afterCreating(function (Blob $blob) {
            // place the video test file in the expected filesystem location

            $path = Storage::disk('local')->putFileAs(
                dirname($blob->file_name),
                new File(base_path('tests/src/video.mp4')),
                basename($blob->file_name));

        });
    }
}
