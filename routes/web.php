<?php

use App\Http\Controllers\Sale\PackageController;
use App\Http\Controllers\Setup\ChapterController;
use App\Http\Controllers\Setup\StudentClassController;
use App\Http\Controllers\Setup\SubjectController;
use App\Http\Controllers\Setup\TopicController;
use App\Http\Controllers\TestSeries\QuestionController;
use App\Http\Controllers\TestSeries\TestSeriesController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // class
    Route::resource('/studentclasses', StudentClassController::class)->except(['destroy']);
    Route::resource('/subject', SubjectController::class)->except(['destroy']);
    Route::resource('/chapter', ChapterController::class)->except(['destroy']);
    Route::resource('/topic', TopicController::class)->except(['destroy']);

    // testserieszer
    Route::resource('/question', QuestionController::class)->except(['destroy']);
    Route::resource('/testseries', TestSeriesController::class)->except(['destroy']);
    Route::resource('/package', PackageController::class)->except(['destroy']);
});
