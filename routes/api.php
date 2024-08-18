<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Middleware\isStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register',[AuthController::class, 'register']);
Route::post('/login-prof',[AuthController::class, 'loginProf']);
Route::post('/login-stud',[AuthController::class, 'loginStud']);
Route::post('/logout',[AuthController::class, 'logout'])->middleware('auth:sanctum');


//student
Route::middleware(['auth:sanctum', isStudent::class])->group(function (){
    Route::get('/stud-profile', [StudentProfileController::class, 'show']);
    Route::post('/create-profile', [StudentProfileController::class, 'store']);
    Route::post('/update-profile', [StudentProfileController::class, 'update']);
});
