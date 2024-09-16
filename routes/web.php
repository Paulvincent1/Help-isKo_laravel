<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\StudentProfileController;
use App\Http\Middleware\Admin\isAdmin;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function(){

    Route::get('/login',[AdminAuthController::class, 'index'])->name('login');
    Route::post('/login-admin',[AdminAuthController::class, 'login'])->name('loginAdmin');

});



Route::middleware([isAdmin::class, 'auth'])->group(function (){

    Route::get('/', function () {
        return view('index');
    })->name('index');

    // Route::get('/announcement', function () {
    //     return view('announcement.announcement');
    // })->name('announcement');

    Route::get('/announcement_add', function () {
        return view('announcement.announcement_add');
    })->name('announcement.announcement_add');

    //FOR ADMIN ANNOUNCEMENT
    // Display all announcements
    Route::get('/announcement', [AnnouncementController::class, 'index'])->name('announcement');

    // Store a new announcement
    Route::post('/announcement', [AnnouncementController::class, 'store'])->name('announcements.store');

    // Update an existing announcement
    Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');

    // Delete an announcement
    Route::delete('/announcement/{id}', [AnnouncementController::class, 'delete'])->name('announcements.delete');

    //student route
    Route::get('/student', function () {
        return view('students.student');
    })->name('student');

    Route::get('/student/add',[StudentProfileController::class, 'index'])->name('students.student_add');
    Route::post('/student/add',[StudentProfileController::class, 'register'])->name('students.student_add_post');

    Route::get('/student_add_profile', function () {
        return view('students.student_add_profile');
    })->name('students.student_add_profile');

    Route::get('/student/hk_duty_quota', [StudentProfileController::class ,'hkQuotaIndex'])->name('students.hkDutyQuota');




    //prof route
    Route::get('/professor', function () {
        return view('professor.professor');
    })->name('professor');

    Route::get('/professor_add', function () {
        return view('professor.professor_add');
    })->name('professor.professor_add');

    Route::get('/professor_add_profile', function () {
        return view('professor.professor_add_profile');
    })->name('professor.professor_add_profile');





});


