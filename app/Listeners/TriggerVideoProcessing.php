<?php

namespace App\Listeners;

use App\Events\UploadCompleted;
use App\Jobs\ConvertVideo;
use App\Jobs\GenerateThumbnailForVideo;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class TriggerVideoProcessing
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\UploadCompleted  $event
     * @return void
     */
    public function handle(UploadCompleted $event)
    {
        Bus::chain([
            new GenerateThumbnailForVideo($event->video),
            new ConvertVideo($event->video),
        ])->dispatch();
    }
}
