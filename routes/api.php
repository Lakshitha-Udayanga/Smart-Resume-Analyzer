<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResumeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Monolog\Registry;

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

Route::post('/user/register', [RegisterController::class, 'store']);
Route::middleware('auth.apiToken')->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::delete('/delete/user/{id}', [LoginController::class, 'destroy']);
    Route::post('/resume/upload', [ResumeController::class, 'upload']);
    Route::get('/users', [LoginController::class, 'index']);
});
