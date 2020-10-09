<?php

use App\Http\Controllers\api\v1\auth\LoginController;
use App\Http\Controllers\api\v1\projects\ProjectsController;
use App\Http\Controllers\api\v1\worktimeentries\WorktimeEntriesController;
use App\Http\Resources\users\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

Route::group([
    'prefix' => 'user',
    'middleware' => ['force-json', 'auth:api']
], function() {
    Route::get('/', function (Request $request) {
        return UserResource::make(Auth::user());
    });
});

/*
|--------------------------------------------------------------------------
| Auth End Points
|--------------------------------------------------------------------------
*/
Route::group([
    'prefix' => 'auth',
    'as' => 'api.auth.',
    'middleware' => ['force-json']
], function() {
    Route::post('/login', LoginController::class)->name('login');
});

/*
|--------------------------------------------------------------------------
| Authenticated End Points
|--------------------------------------------------------------------------
*/
Route::group([
    'as' => 'api.',
    'middleware' => ['force-json', 'auth:api']
], function() {

    /*
    |--------------------------------------------------------------------------
    | Team End Points
    |--------------------------------------------------------------------------
    */
    Route::group([
        'prefix' => 'teams',
        'as' => 'teams.',
    ], function() {
        Route::post('/login', LoginController::class)->name('login');
    });

    /*
    |--------------------------------------------------------------------------
    | Project End Points
    |--------------------------------------------------------------------------
    */
    Route::resource('projects', ProjectsController::class)->only(['index', 'show',]);

    /*
    |--------------------------------------------------------------------------
    | Worktime entry End Points
    |--------------------------------------------------------------------------
    */
    Route::resource('worktime-entries', WorktimeEntriesController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
});
