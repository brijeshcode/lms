<?php

use App\Http\Controllers\API\ClassController;
use App\Http\Controllers\API\LiveClassController;
use App\Http\Controllers\API\StudentController;
use App\Http\Controllers\API\TestSeriesController;
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

$version = 'v1';
Route::prefix($version)->group(function () {
    Route::post('/register', [StudentController::class, 'register']);
    Route::post('/login', [StudentController::class, 'login']);
});

Route::fallback(function () {
    $response = [
        'status' => 'error',
        'message' => 'Not Found'
    ];
    return response()->json($response, 404);
});


Route::middleware('auth:sanctum')->prefix($version)->group(function () {
    Route::post('/logout', [StudentController::class, 'logout']);

    Route::get('/classes', [ClassController::class, 'allClasses']);
    Route::get('/classes/{class}/subjects', [ClassController::class, 'classSubjects']);
    Route::get('/classes/{class}/subjects/{subject}/chapters', [ClassController::class, 'classChapters']);
    Route::get('/subjects/{subject}/chapters', [ClassController::class, 'subjectChapters']);
    Route::get('/live-classes', [LiveClassController::class, 'allClasses']);
    Route::get('/live-classes/{liveClassId}/sessions', [LiveClassController::class, 'classSessions']);

    Route::get('/testseries', [TestSeriesController::class, 'getTests']);
    Route::get('/testseries/{testSeries}', [TestSeriesController::class, 'getTest']);
});