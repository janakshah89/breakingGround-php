<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get("/clientTestimonials","App\Http\Controllers\ClientTestimonialsController@listItems");
Route::get("/sitestatics", "App\Http\Controllers\SiteStaticsController@listItems");
Route::get("/sitestatics/{slug}", "App\Http\Controllers\SiteStaticsController@getItems");

Route::get("/location", "App\Http\Controllers\CommonController@listLocation");
Route::get("/location_market", "App\Http\Controllers\CommonController@listLocationMicroMarket");
Route::get("/location/{location}/market", "App\Http\Controllers\CommonController@listLocationMicroMarket");
Route::post("login", "App\Http\Controllers\CommonController@login");

Route::get("/properties", "App\Http\Controllers\PropertyMasterController@list");
Route::get("/properties/{id}", "App\Http\Controllers\PropertyMasterController@show");
Route::post("properties", "App\Http\Controllers\PropertyMasterController@create");
Route::post("upload", "App\Http\Controllers\PropertyMasterController@upload");

Route::get("/similarproperties/{id}", "App\Http\Controllers\PropertyMasterController@similar_property");

Route::fallback(
    function () {
        return response()->json(
            [
                'file' => __FILE__,
                'line' => __LINE__,
                'code' => 404,
                'message' => 'Not Found',
                'trace' => null,
                'response' => [],
            ],
            404,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
)->name('Api.NotFound');
