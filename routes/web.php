<?php

use App\Http\Controllers\API\ResumeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClientListController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobsListController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TrainingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
    return view('auth.login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/registered/client/export', [ClientListController::class, 'exportExcel'])->name('client.export');
    Route::resource('/registered/client', ClientListController::class);
    Route::resource('/dashboard', DashboardController::class);
    Route::resource('/jobs', JobsListController::class);
    Route::post('/jobs/import', [JobsListController::class, 'import'])->name('jobs.import');
    Route::resource('/resumes', \App\Http\Controllers\ResumeListController::class)->only(['index', 'destroy']);

    // check pdf
    Route::get('/view-pdf', [ResumeController::class, 'viewIndexPdf']);
    Route::post('/pdf/summarize', [ResumeController::class, 'summarizePdf']);
    Route::post('/get/jobs', [ResumeController::class, 'getRecommendations']);
    Route::get('/resumes/export', [ResumeController::class, 'exportResumes'])->name('resumes.export');

    Route::get('/data-training', [TrainingController::class, 'index'])->name('training.index');
    Route::post('/data-training/process', [TrainingController::class, 'process'])->name('training.process');
    Route::get('/data-training/export', [TrainingController::class, 'export'])->name('training.export');
});
