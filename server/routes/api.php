<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompletedLessonController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\SetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/courses', [CourseController::class, 'getAll']);
    Route::get('/courses/{course_slug}', [CourseController::class, 'getDetail']);

    Route::middleware('isUser')->group(function () {
        Route::get('/users/progress', [EnrollmentController::class, 'getProgress']);

        Route::post('/lessons/{lesson_id}/contents/{contents_id}/check', [OptionController::class, 'checkAnswer']);

        Route::put('/lessons/{lesson_id}/completed', [CompletedLessonController::class, 'track']);

        Route::post('/courses/{course_slug}/register', [EnrollmentController::class, 'register']);
    });

    Route::middleware('isAdmin')->group(function () {
        Route::post('/courses', [CourseController::class, 'store']);
        Route::put('/courses/{course_slug}', [CourseController::class, 'update']);
        Route::delete('/courses/{course_slug}', [CourseController::class, 'destroy']);

        Route::post('/courses/{course}/sets', [SetController::class, 'store']);
        Route::delete('/courses/{course}/sets/{set_id}', [SetController::class, 'destroy']);

        Route::post('/lessons', [LessonController::class, 'store']);
        Route::delete('/lessons/{lesson_id}', [LessonController::class, 'destroy']);
    });

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('login');
});

Route::middleware('guest')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])
        ->name('register');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login');
});
