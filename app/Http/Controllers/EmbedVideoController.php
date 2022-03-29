<?php

namespace App\Http\Controllers;

use App\Enums\BlobRoles;
use App\Models\Video;
use Illuminate\Http\Request;

class EmbedVideoController extends Controller
{


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function show(Video $video)
    {
        // TODO: might be possible to deliver the converted h264 to webM video in case of need

        $thumbnail = optional($video->blobs->where('role', BlobRoles::THUMBNAIL)->first())->url;
        
        $thumbnailStrip = optional($video->blobs->where('role', BlobRoles::THUMBNAIL_STRIP)->first())->url;
        
        $hlsPlaylist = optional($video->blobs->where('role', BlobRoles::VIDEO)->first())->hls_playlist_url;

        return view('videos.embed', [
            'video' => $video,
            'title' => $video->title,
            'thumbnail' => $thumbnail,
            'thumbnail_strip' => $thumbnailStrip,
            'hls_playlist' => $hlsPlaylist,
        ]);
    }

}
