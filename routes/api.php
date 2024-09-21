<?php

use App\Http\Controllers\Api\AnnouncementController;
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
use App\Http\Controllers\EmployeeProfileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfessorProfileController;
use App\Http\Middleware\isEmployee;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login-employee', [AuthController::class, 'loginEmployee']);
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

Route::middleware(['auth:sanctum', isEmployee::class])->group(function (){

//Prof nirerate si Student.
Route::post('/feedback/{student_id}', [StudentFeedbackController::class, 'store']);

//PROFESSOR DUTY APIS
//creation and listing info regarding request
Route::post('/professors/duties/create', [DutyProfController::class, 'create']);
Route::get('/professors/duties', [DutyProfController::class, 'index']);
Route::get('/professors/duties/{dutyId}', [DutyProfController::class, 'show']);
Route::get('/professors/{profId}/duties/requests', [DutyProfController::class, 'getRequestsForAllDuties']);

//Executable as long as prof has no accepted request
Route::put('/professors/updateInfo/{dutyId}', [DutyProfController::class, 'update']);
Route::delete('/professors/duties/{dutyId}', [DutyProfController::class, 'delete']);

// For accepting and rejecting a request for a duty from students
Route::post('/professors/requests/{recordId}/accept', [DutyProfController::class, 'acceptStudent']);
Route::delete('/professors/requests/{recordId}/reject', [DutyProfController::class, 'rejectStudent']);

//Show status of duties with accepted students
Route::get('/professors/duties/{dutyId}/accepted-students', [DutyProfController::class, 'getAcceptedStudents']);
//Can update the duty_status
Route::patch('/duties/{dutyId}/status', [DutyProfController::class, 'updateStatus']);
// Cancel a duty regardless
Route::delete('/duties/{dutyId}/cancel', [DutyProfController::class, 'cancelDuty']);
//Good alternative for locking a duty regardless of current_scholars
Route::post('/professors/duties/{dutyId}/lock', [DutyProfController::class, 'lockDuty']);



    //employee profile
    Route::get('/employee-profile', [EmployeeProfileController::class, 'index']);
    Route::get('/employee-profile/{id}', [EmployeeProfileController::class, 'show']);
    Route::post('/employee-profile/create', [EmployeeProfileController::class, 'create']);
    Route::put('/employee-profile/update', [EmployeeProfileController::class, 'update']);

});


//admin
Route::middleware(['auth:sanctum', isAdmin::class])->group(function () {
    Route::post('/create-hk-status', [HkStatusController::class, 'store']);


    // Announcement
    Route::get('/announcements',[AnnouncementController::class, 'index']);
    Route::get('/announcement/{id}',[AnnouncementController::class, 'show']);
    Route::post('/create-announcement',[AnnouncementController::class, 'store']);
    Route::put('/update-announcement/{id}',[AnnouncementController::class, 'update']);
    Route::put('/delete-announcement/{id}',[AnnouncementController::class, 'delete']);
});


  // General duty routes accessible to students and professors
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/duties', [DutyController::class, 'index']);
    Route::get('/duties/{dutyId}', [DutyController::class, 'show']);
    Route::get('/duties/status/{dutyId}', [DutyController::class, 'checkStatus']);


    // chat routes
    Route::get('/existing-chat-users',[MessageController::class, 'existingChats']);
    Route::get('/view-messages/{id}',[MessageController::class,'viewMessages']);
    Route::post('/send-message/{id}', [MessageController::class,'sendMessage']);
});
