<?php

namespace App\Jobs;

use App\Enums\BlobRoles;
use App\Enums\ConversionTargets;
use App\Models\Blob;
use App\Models\Video;
use FFMpeg\Format\Video\WebM;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Filters\TileFactory;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class ConvertVideo implements ShouldQueue
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

        $webMPath = "{$blob->disk_folder}/{$blob->uuid}.webm";
        $hlsPlaylist = "{$blob->disk_folder}/hls/{$blob->uuid}.m3u8";

        $lowBitrate = (new X264)->setKiloBitrate(250);
        $midBitrate = (new X264)->setKiloBitrate(500);
        $highBitrate = (new X264)->setKiloBitrate(1000);

        FFMpeg::fromDisk($blob->disk)
            ->open($blob->file_name)

            // TODO: decide if WebM conversion should be separate from HLS
            // ->export()
            // ->toDisk($blob->conversions_disk)
            // ->onProgress(function ($percentage) {
            //     // TODO: get transcoding progress echo "{$percentage}% transcoded";
            //     logs()->info("WebM {$percentage}% transcoded");
            // })
            // ->inFormat(new WebM)
            // ->save($webMPath)
            
            ->exportForHLS()
            ->onProgress(function ($percentage) {
                logs()->info("{$percentage}% transcoded");
            })
            ->addFormat($lowBitrate)
            ->addFormat($midBitrate)
            ->addFormat($highBitrate)

            // TODO: include scaling factors otherwise all streams will have the same resolution with different target bitrate
            // ->addFormat($lowBitrate, function($media) {
            //     $media->addFilter('scale=640:480');
            // })
            // ->addFormat($midBitrate, function($media) {
            //     $media->scale(960, 720);
            // })

            ->toDisk($blob->conversions_disk)
            ->save($hlsPlaylist);

        // TODO: lock for update and handle already existing conversions
        // TODO: if one of the transcoding fails we might have troubles, so better to separate them
        $blob->conversions = [
            [
                'type' => ConversionTargets::HLS,
                'file_name' => $hlsPlaylist,
            ],
            // [
            //     'type' => ConversionTargets::WEBM,
            //     'file_name' => $webMPath,
            // ]
        ];

        $blob->save();
        

    }
}
