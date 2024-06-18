<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Middleware\PermitMiddleware;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::apiResource('teachers', TeacherController::class);

// Route::middleware(['permit:read,students'])->get('/students', [StudentController::class, 'index']);
// Route::middleware(['permit:create,students'])->post('/students', [StudentController::class, 'store']);
// Route::middleware(['permit:read,students'])->get('/students/{student}', [StudentController::class, 'show']);
// Route::middleware(['permit:update,students'])->put('/students/{student}', [StudentController::class, 'update']);
// Route::middleware(['permit:delete,students'])->delete('/students/{student}', [StudentController::class, 'destroy']);

Route::apiResource('students', StudentController::class);
