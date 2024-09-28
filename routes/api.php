<?php

use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\StudentFeedbackController;
use App\Models\HkStatus;
use Illuminate\Http\Request;
use App\Http\Middleware\isAdmin;
use App\Http\Middleware\isStudent;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HkStatusController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\Duty\DutyController;
use App\Http\Controllers\Duty\StudentDutyController;
use App\Http\Controllers\Duty\EmployeeDutyController;
use App\Http\Controllers\EmployeeProfileController;
use App\Http\Controllers\MessageController;
use App\Http\Middleware\isEmployee;
use App\Http\Controllers\Duty\DutyNotificationsController;

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


    //employee profile
    Route::get('/employee-profile', [EmployeeProfileController::class, 'index']);
    Route::get('/employee-profile/{id}', [EmployeeProfileController::class, 'show']);
    Route::post('/employee-profile/create', [EmployeeProfileController::class, 'create']);
    Route::put('/employee-profile/update', [EmployeeProfileController::class, 'update']);

//EMPLOYEE DUTY APIS
    // Creation and listing info regarding request
    Route::post('/employees/duties/create', [EmployeeDutyController::class, 'create']);
    
    Route::get('/employees/duty', [EmployeeDutyController::class, 'index']);
    Route::get('/employees/duties/{dutyId}', [EmployeeDutyController::class, 'show']);
    Route::get('/employees/duties/requests', [EmployeeDutyController::class, 'getRequestsForAllDuties']);
    
    // As long as employee has no accepted request, employee can delete or update info of the duty
    Route::put('/employees/updateInfo/{dutyId}', [EmployeeDutyController::class, 'update']); 
    Route::delete('/employees/duties/{dutyId}', [EmployeeDutyController::class, 'delete']);

    // For accepting and rejecting a request for a duty from students
    Route::post('/employees/requests/accept', [EmployeeDutyController::class, 'acceptStudent']);
    Route::delete('/employees/requests/reject', [EmployeeDutyController::class, 'rejectStudent']);

    // Show status of duties with accepted students
    Route::get('/employees/duties/{dutyId}/accepted-students', [EmployeeDutyController::class, 'getAcceptedStudents']);
    Route::get('/employee/accepted-student-names', [EmployeeDutyController::class, 'getAcceptedStudentNames']);

    // Can update the duty_status
    Route::patch('/duties/{dutyId}/status', [EmployeeDutyController::class, 'updateStatus']);
    // Cancel a duty regardless
    Route::delete('/duties/{dutyId}/cancel', [EmployeeDutyController::class, 'cancelDuty']);

//admin
Route::middleware(['auth:sanctum', isAdmin::class])->group(function () {
    Route::post('/create-hk-status', [HkStatusController::class, 'store']);


    // Announcement
    // Route::get('/announcement',[AnnouncementController::class, 'index']);
    Route::get('/announcement/{id}',[AnnouncementController::class, 'show']);
    Route::post('/create-announcement',[AnnouncementController::class, 'store']);
    Route::put('/update-announcement/{id}',[AnnouncementController::class, 'update']);
    Route::put('/delete-announcement/{id}',[AnnouncementController::class, 'delete']);
});


  // General duty routes accessible to students and employees
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/announcements',[AnnouncementController::class, 'index']);
    Route::get('/duties', [DutyController::class, 'index']);
    Route::get('/duties/{dutyId}', [DutyController::class, 'show']);
    Route::get('/duties/status/{dutyId}', [DutyController::class, 'checkStatus']);
    

    // chat routes
    Route::get('/existing-chat-users',[MessageController::class, 'existingChats']);
    Route::get('/view-messages/{id}',[MessageController::class,'viewMessages']);
    Route::post('/send-message/{id}', [MessageController::class,'sendMessage']);

    Route::get('/notifications', function (Request $request) {
        // Return the authenticated user's notifications
        return $request->user()->notifications;
    });

    Route::get('duty/notifications', [DutyNotificationsController::class, 'index']);
});
});