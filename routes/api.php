<?php

use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\StudentFeedbackController;
use App\Http\Controllers\HkStatusController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\Duty\DutyController;
use App\Http\Controllers\Duty\StudentDutyController;
use App\Http\Controllers\Duty\EmployeeDutyController;
use App\Http\Controllers\EmployeeProfileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Duty\DutyNotificationsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\isAdmin;
use App\Http\Middleware\isStudent;
use App\Http\Middleware\isEmployee;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login-employee', [AuthController::class, 'loginEmployee']);
Route::post('/login-stud', [AuthController::class, 'loginStud']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Password reset routes
Route::post('/forgot', [AuthController::class, 'forgot']);
Route::put('/reset', [AuthController::class, 'resetpassword']);

// Student routes
Route::middleware(['auth:sanctum', isStudent::class])->group(function () {
    // Profile
    Route::get('/stud-profile', [StudentProfileController::class, 'show']);
    Route::post('/stud-profile/create', [StudentProfileController::class, 'store']);
    Route::post('/stud-profile/update', [StudentProfileController::class, 'update']);

    // HK Status
    Route::get('/hk-status', [HkStatusController::class, 'show']);

    // Duties record of students
    Route::get('/students/duties/available', [StudentDutyController::class, 'viewAvailableDuties']);
    Route::post('/students/duties/{dutyId}/request', [StudentDutyController::class, 'requestDuty']);
    Route::get('/students/duties/requested', [StudentDutyController::class, 'viewRequestedDuties']);
    Route::get('/students/duties/{dutyId}/details', [StudentDutyController::class, 'viewRequestedDutyDetails']);
    Route::delete('/students/duties/{dutyId}/cancel', [StudentDutyController::class, 'cancelRequest']);
    Route::get('/students/duties/accepted', [StudentDutyController::class, 'viewAcceptedDuties']);
    Route::get('/students/duties/completed', [StudentDutyController::class, 'viewCompletedDuties']);
});

// Feedback routes
Route::get('/feedback/{id}', [StudentFeedbackController::class, 'show']);
Route::get('/feedback/index/{student_id}', [StudentFeedbackController::class, 'index']);

// Professor routes
Route::middleware(['auth:sanctum', isEmployee::class])->group(function () {
    // Professor rates student feedback
    Route::post('/feedback/{student_id}', [StudentFeedbackController::class, 'store']);

    // Employee profile
    Route::get('/employee-profile', [EmployeeProfileController::class, 'index']);
    Route::get('/employee-profile/{id}', [EmployeeProfileController::class, 'show']);
    Route::post('/employee-profile/create', [EmployeeProfileController::class, 'create']);
    Route::put('/employee-profile/update', [EmployeeProfileController::class, 'update']);

    // Employee duties APIs
    Route::post('/employees/duties/create', [EmployeeDutyController::class, 'create']);
    Route::get('/employees/duty', [EmployeeDutyController::class, 'index']);
    Route::get('/employees/duties/{dutyId}', [EmployeeDutyController::class, 'show']);
    Route::get('/employees/duties/requests', [EmployeeDutyController::class, 'getRequestsForAllDuties']);

    // Duty management
    Route::put('/employees/updateInfo/{dutyId}', [EmployeeDutyController::class, 'update']);
    Route::delete('/employees/duties/{dutyId}', [EmployeeDutyController::class, 'delete']);
    Route::post('/employees/requests/accept', [EmployeeDutyController::class, 'acceptStudent']);
    Route::delete('/employees/requests/reject', [EmployeeDutyController::class, 'rejectStudent']);
    Route::get('/employees/duties/{dutyId}/accepted-students', [EmployeeDutyController::class, 'getAcceptedStudents']);
    Route::get('/employee/accepted-student-names', [EmployeeDutyController::class, 'getAcceptedStudentNames']);
    Route::patch('/duties/{dutyId}/status', [EmployeeDutyController::class, 'updateStatus']);
    Route::delete('/duties/{dutyId}/cancel', [EmployeeDutyController::class, 'cancelDuty']);
});

// Admin routes
Route::middleware(['auth:sanctum', isAdmin::class])->group(function () {
    // HK Status
    Route::post('/create-hk-status', [HkStatusController::class, 'store']);

    // Announcements
    // Route::get('/announcements', [AnnouncementController::class, 'index']);
    Route::get('/announcement/{id}', [AnnouncementController::class, 'show']);
    Route::post('/create-announcement', [AnnouncementController::class, 'store']);
    Route::put('/update-announcement/{id}', [AnnouncementController::class, 'update']);
    Route::put('/delete-announcement/{id}', [AnnouncementController::class, 'delete']);
});

// General duty routes accessible to students and employees
Route::middleware(['auth:sanctum'])->group(function () {
    // Duties
    Route::get('/announcements', [AnnouncementController::class, 'index']);
    Route::get('/duties', [DutyController::class, 'index']);
    Route::get('/duties/{dutyId}', [DutyController::class, 'show']);
    Route::get('/duties/status/{dutyId}', [DutyController::class, 'checkStatus']);

    // Chat routes
    Route::get('/existing-chat-users', [MessageController::class, 'existingChats']);
    Route::get('/view-messages/{id}', [MessageController::class, 'viewMessages']);
    Route::post('/send-message/{id}', [MessageController::class, 'sendMessage']);

    // Notifications
    Route::get('/notifications', function (Request $request) {
        return $request->user()->notifications;
    });

    // Duty notifications
    Route::get('duty/notifications', [DutyNotificationsController::class, 'index']);
});
