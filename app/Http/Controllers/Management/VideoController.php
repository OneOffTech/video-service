<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Blob;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('videos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // FileName sanitizer return str_replace(['#', '/', '\\', ' '], '-', $fileName);

        $validated = $request->validate([
            'title' => 'required|string|max:250|min:4',
            'description' => 'present|nullable|string',
            'language' => 'present|nullable|string|max:4',
            // 'tags' => '',
            'license' => 'present|nullable|string|max:250',
            'file' => 'required|file|mimetypes:video/mp4|max:204800',
        ]);

        // Move the uploaded file

        /** @var UploadedFile $file */
        $file = $validated['file'];

        $hashName = $file->hashName();
        $hashFolder = Str::before($hashName, '.');

        $uploadPath = $file->storeAs("video/{$hashFolder}", $hashName, 'local');

        $blob = new Blob([
            'disk' => 'local',
            'name' => $file->getClientOriginalName(),
            'file_name' => $uploadPath,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        $validated['user_id'] = $request->user()->getKey();


        $video = DB::transaction(function() use ($validated, $blob){

            $videoEntry = Video::create(Arr::only($validated, ['title', 'description', 'language', 'license', 'user_id']));

            $videoEntry->blobs()->save($blob);

            return $videoEntry;
        });


        return response()->redirectToRoute('videos.edit', [$video->uuid]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function show(Video $video)
    {
        $video->load(['user', 'blobs']);

        return view('videos.show', ['video' => $video]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function edit(Video $video)
    {
        $video->load(['user', 'blobs']);

        return view('videos.edit', ['video' => $video]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Video $video)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function destroy(Video $video)
    {
        //
    }
}
