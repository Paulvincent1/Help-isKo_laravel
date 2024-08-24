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
use App\Http\Controllers\Duty\DutyController;
use App\Http\Controllers\Duty\StudentDutyController;
use App\Http\Controllers\Duty\DutyProfController;
use App\Http\Controllers\ProfessorProfileController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login-prof', [AuthController::class, 'loginProf']);
Route::post('/login-stud', [AuthController::class, 'loginStud']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

//reset password
Route::post('/forgot', [AuthController::class, 'forgot']);
Route::put('/reset', [AuthController::class, 'resetpassword']);


//student

Route::middleware(['auth:sanctum', isStudent::class])->group(function () {
    Route::get('/stud-profile', [StudentProfileController::class, 'show']);
    Route::post('/stud-profile/create', [StudentProfileController::class, 'store']);
    Route::post('/stud-profile/update', [StudentProfileController::class, 'update']);


    Route::get('/hk-status', [HkStatusController::class, 'show']);

//duties record of students
Route::get('/students/duties/available', [StudentDutyController::class, 'viewAvailableDuties']);
Route::post('/students/duties/{dutyId}/request', [StudentDutyController::class, 'requestDuty']);
Route::get('/students/duties/requested', [StudentDutyController::class, 'viewRequestedDuties']);
Route::get('/students/duties/{dutyId}/details', [StudentDutyController::class, 'viewRequestedDutyDetails']);
Route::delete('/students/duties/{dutyId}/cancel', [StudentDutyController::class, 'cancelRequest']);
Route::get('/students/duties/accepted', [StudentDutyController::class, 'viewAcceptedDuties']);
Route::get('/students/duties/completed', [StudentDutyController::class, 'viewCompletedDuties']);
    });

//Feedback show
Route::get('/feedback/{id}', [StudentFeedbackController::class, 'show']);

//show specific student feedbacks
Route::get('/feedback/index/{student_id}', [StudentFeedbackController::class, 'index']);


//PROFESSOR

Route::middleware(['auth:sanctum', isProfessor::class])->group(function (){

//Prof nirerate si Student.
Route::post('/feedback/{student_id}', [StudentFeedbackController::class, 'store']);    

//PROFESSOR DUTY APIS
//Create a new duty - (new created duties default value set to duty_status = pending)
Route::post('/professors/duties/create', [DutyProfController::class, 'create']);

//Get function for the prof to display necessary info for his duties
Route::get('/professors/duties', [DutyProfController::class, 'index']);

//Get function for prof to get details of one specific duty
Route::get('/professors/duties/{dutyId}', [DutyProfController::class, 'show']);

// Get function para makuha kung ilang numbers of request and names ng mga nagrequest sa duty ng Prof
Route::get('/professors/duties/{dutyId}/requests', [DutyProfController::class, 'getRequestsForDuty']);

// Put function for the prof to update duties info as long as there's no accepted request 
Route::put('/professors/updateInfo/{dutyId}', [DutyProfController::class, 'update']);

// Delete function for a duty na wala pang accepted request si prof 
Route::delete('/professors/duties/{dutyId}', [DutyProfController::class, 'delete']);

// For accepting and rejecting a request for a duty from students
Route::post('/professors/duties/{dutyId}/accept/{studentId}', [DutyProfController::class, 'acceptStudent']);
Route::post('/professors/duties/{dutyId}/reject/{studentId}', [DutyProfController::class, 'rejectStudent']);

//Show status of duties with accepted students
Route::get('/professors/duties/{dutyId}/accepted-students', [DutyProfController::class, 'getAcceptedStudents']);

// Prof can only update the duty_status("Pending, Active, Completed");
    Route::patch('/duties/{dutyId}/status', [DutyProfController::class, 'updateStatus']);
   
// Cancel a duty (duty_status = cancelled) only once
Route::delete('/duties/{dutyId}/cancel', [DutyProfController::class, 'cancelDuty']);

//Simple function to lock duty - no purpose
Route::post('/professors/duties/{dutyId}/lock', [DutyProfController::class, 'lockDuty']);



    //prof profile
    Route::get('/prof-profile', [ProfessorProfileController::class, 'index']);
    Route::get('/prof-profile/{id}', [ProfessorProfileController::class, 'show']);
    Route::post('/prof-profile/create', [ProfessorProfileController::class, 'create']);
    Route::put('/prof-profile/update', [ProfessorProfileController::class, 'update']);

});


//admin
Route::middleware(['auth:sanctum', isAdmin::class])->group(function () {
    Route::post('/create-hk-status', [HkStatusController::class, 'store']);
});


  // General duty routes accessible to students and professors
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/duties', [DutyController::class, 'index']);
    Route::get('/duties/{dutyId}', [DutyController::class, 'show']);
    Route::get('/duties/status/{dutyId}', [DutyController::class, 'checkStatus']);
});
