<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OembedController;
use App\Http\Controllers\EmbedVideoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// OEmbed Endpoint route. See https://oembed.com/
Route::middleware('throttle:60,1')->get('/oembed', [OembedController::class, 'show'])->name('oembed.json');

// Page with video player for embedding in other sites
Route::get('/embed/{video}', [EmbedVideoController::class, 'show'])->name('videos.embed');
