<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ResumeController;
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

Route::middleware('auth.apiToken')->group(function () {
    //password reset
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink']);
    Route::post('/reset-password', [ResetPasswordController::class, 'reset']);

    //user register
    Route::post('/user/register', [RegisterController::class, 'store']);

    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/delete/user/{id}', [LoginController::class, 'destroy']);
    Route::post('/resume/upload/{user_id}', [ResumeController::class, 'upload']);
    Route::get('/users', [LoginController::class, 'index']);
    Route::post('/update/user/{id}', [RegisterController::class, 'update']);
});
