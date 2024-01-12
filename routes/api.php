<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginRegisterController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


// Public routes of authtication
Route::controller(LoginRegisterController::class)->prefix('v1')->group(function() {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});

// Protected routes of logout
Route::middleware('auth:sanctum')->prefix('v1')->group( function () {
    Route::post('/logout', [LoginRegisterController::class, 'logout']);
});
