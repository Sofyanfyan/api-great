<?php

use App\Http\Controllers\Authentication\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware(['auth.guard'])->prefix('users')->group(function () {
    Route::post('email-verifications', [UserController::class, 'verification']);
});

Route::middleware(['email.verified'])->prefix('users')->group(function () {
    Route::post('dashboard', [UserController::class, 'test']);
});