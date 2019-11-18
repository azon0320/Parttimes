<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*

Auto-generated route..

Route::get('/', function () {
    return view('welcome');
});
*/

use Illuminate\Support\Facades\Route;

#
# Static Resource
#
Route::get('/avatar/{userid}', function($uid){
    $disk = \Illuminate\Support\Facades\Storage::disk('parttime_avatars');
    $fname = "$uid.jpg";
    return $disk->exists($fname) ?
        \Intervention\Image\Facades\Image::make($disk->readStream($fname))->response() :
        response("not found", 404);
});

Route::get('/imgs/{parttime_id}/{index}', function($parttime_id, $index){
    $disk = \Illuminate\Support\Facades\Storage::disk('parttime_images');
    $path = "$parttime_id/img_$index.jpg";
    return $disk->exists($path) ?
        \Intervention\Image\Facades\Image::make($disk->readStream($path))->response() :
        response("not found", 404);
});