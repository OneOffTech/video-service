<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OembedController extends Controller
{
    /**
     * Return the oEmbed (https://oembed.com/) response for the specified request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $format = $request->input('format', 'json');
        $url = $request->input('url', '');
        $maxwidth = $request->input('maxwidth', 960);
        $maxheight = $request->input('maxheight', 540);

        // if format is not json, abort
        abort_if($format !== 'json', 501);

        // Get the document id from the given URL
        // TODO: maybe there will be additional routes as currently we refer to the authenticated route to show a video
        $base_url = route('videos.show', '').'/';

        // if the url don't start with this application URL, or
        // contains query parameters => abort
        abort_unless(Str::startsWith($url, $base_url), 404);
        abort_if(Str::contains($url, '?') || Str::contains($url, '#'), 404);
        
        $id = e(str_replace($base_url, '', $url));

        $document = Video::whereUuid($id)->first();

        // if document not found simply return
        abort_if(is_null($document), 404);

        $width = e($maxwidth);
        $height = e($maxheight);

        // the URL that will be used in the resulting iframe to show the embed
        $embed_url = route('videos.embed', $document->uuid);

        $data = [
            "version" => "1.0",
            "type" => "rich",
            "provider_name" => config('app.name'),
            "provider_url" => config('app.url'),
            "width" => $width,
            "height" => $height,
            "title" => e($document->title), // optional
            "html" => "<iframe width=\"$width\" height=\"$height\" src=\"$embed_url\" class=\"ootvs-embed\" frameborder=\"0\" allowfullscreen></iframe>",
        ];

        return response()->json($data, 200);
    }
}
