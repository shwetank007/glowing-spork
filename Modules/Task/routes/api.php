<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Task\app\Http\Controllers\TaskController;

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

// Protected routes of task
Route::middleware('auth:sanctum')->prefix('v1/task')->group( function () {
    Route::get('/listing', [TaskController::class, 'listing']);
    Route::post('/save', [TaskController::class, 'store']);
});