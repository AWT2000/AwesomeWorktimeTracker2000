<?php

use App\Http\Controllers\api\rfidclientapi\v1\UsersController as RfidClientUsersController;
use App\Http\Controllers\api\rfidclientapi\v1\WorktimeEntryController;
use App\Http\Controllers\api\v1\auth\LoginController;
use App\Http\Controllers\api\v1\projects\ProjectsController;
use App\Http\Controllers\api\v1\worktimeentries\WorktimeEntriesController;
use App\Http\Resources\users\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group([
    'as' => 'rfidclientapi.',
    'middleware' => ['force-json', 'client']
], function() {
    Route::get('/users/{rfidTagString}', [RfidClientUsersController::class, 'index'])->name('users');

    Route::post('worktime-entries', [WorktimeEntryController::class, 'store'])->name('worktime-entries.store');
    Route::put('worktime-entries/{worktime_entry}', [WorktimeEntryController::class, 'update'])->name('worktime-entries.update');
});
