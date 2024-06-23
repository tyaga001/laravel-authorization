<?php

use App\Http\Controllers\PermitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::apiResource('teachers', TeacherController::class);

Route::post('/roles', [PermitController::class, 'createRole']);
Route::post('/resources', [PermitController::class, 'createResource']);
Route::post('/role-assignments', [PermitController::class, 'assignRole']);

Route::apiResource('students', StudentController::class);
