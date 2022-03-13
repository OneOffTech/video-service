<?php

namespace App\Jobs;

use App\Enums\BlobRoles;
use App\Models\Blob;
use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Filters\TileFactory;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class GenerateThumbnailForVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \App\Models\Video 
     */
    public $video;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $blob = $this->video->blobs()->video()->first();

        $thumbnailPath = "{$blob->disk_folder}/thumbnail.jpg";
        $thumbnailTilePath = "{$blob->disk_folder}/thumbnail-tile.jpg";
        $thumbnailTileVTT = "{$blob->disk_folder}/thumbnail-tile.vtt";

        FFMpeg::fromDisk($blob->disk)
            ->open($blob->file_name)
            // TODO: with ffmpeg was possible to get the most significant frame automatically, maybe this can be an improvement
            ->getFrameFromSeconds(2)
            ->export()
            ->toDisk($blob->conversions_disk)
            ->save($thumbnailPath)
            ->exportTile(function (TileFactory $factory) use ($thumbnailTileVTT) {
                // TODO: probably we can improve by adapting grid and interval to the video duration
                $factory->interval(10)
                    ->scale(320, 180)
                    ->grid(5, 5)
                    ->generateVTT($thumbnailTileVTT);
            })
            ->toDisk($blob->conversions_disk)
            ->save($thumbnailTilePath);

        $this->video->blobs()->createMany([
            [
                'disk' => $blob->conversions_disk,
                'role' => BlobRoles::THUMBNAIL,
                'name' => basename($thumbnailPath),
                'file_name' => $thumbnailPath,
                'mime_type' => 'image/jpeg',
                'size' => Storage::disk($blob->conversions_disk)->size($thumbnailPath),
            ],
            [
                'disk' => $blob->conversions_disk,
                'role' => BlobRoles::THUMBNAIL_STRIP,
                'name' => basename($thumbnailTilePath),
                'file_name' => $thumbnailTilePath,
                'mime_type' => 'image/jpeg',
                'size' => Storage::disk($blob->conversions_disk)->size($thumbnailTilePath),
            ],
        ]);

    }
}
