<?php


use App\Http\Controllers\Api\StudentFeedbackController;
use App\Models\HkStatus;
use Illuminate\Http\Request;
use App\Http\Middleware\isAdmin;
use App\Http\Middleware\isStudent;
use App\Http\Middleware\isProfessor;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HkStatusController;
use App\Http\Controllers\StudentProfileController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login-prof', [AuthController::class, 'loginProf']);
Route::post('/login-stud', [AuthController::class, 'loginStud']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


//student
Route::middleware(['auth:sanctum', isStudent::class])->group(function () {
    Route::get('/stud-profile', [StudentProfileController::class, 'show']);
    Route::post('/create-profile', [StudentProfileController::class, 'store']);
    Route::post('/update-profile', [StudentProfileController::class, 'update']);


    Route::get('/hk-status', [HkStatusController::class, 'show']);

});

//Feedback show
Route::get('/feedback/{id}', [StudentFeedbackController::class, 'show']);

//show specific student feedbacks
Route::get('/feedback/index/{student_id}', [StudentFeedbackController::class, 'index']);


//professor

Route::middleware(['auth:sanctum', isProfessor::class])->group(function (){

//Prof nirerate si Student.
Route::post('/feedback/{student_id}', [StudentFeedbackController::class, 'store']);    


});


//admin
Route::middleware(['auth:sanctum', isAdmin::class])->group(function () {
    Route::post('/create-hk-status', [HkStatusController::class, 'store']);
});
