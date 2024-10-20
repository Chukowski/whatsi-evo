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

Route::middleware('auth:api')->get('/whatsi', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'whatsi',
    'namespace' => 'Modules\Whatsi\Http\Controllers'
], function () {
    Route::post('/connect', 'Main@connect');
    Route::get('/status', 'Main@status');
    Route::delete('/delete', 'Main@delete');
});
