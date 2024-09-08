<?php


use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\ProfessorProfileController as AdminProfessorProfileController;
use App\Http\Controllers\Admin\StudentProfileController;
use App\Http\Middleware\Admin\isAdmin;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AdminAuthController::class, 'index'])->name('login');
    Route::post('/login-admin', [AdminAuthController::class, 'login'])->name('loginAdmin');
});



Route::middleware([isAdmin::class, 'auth'])->group(function () {

    Route::get('/', function () {
        return view('index');
    })->name('index');

    Route::get('/announcement', function () {
        return view('announcement.announcement');
    })->name('announcement');

    Route::get('/announcement_add', function () {
        return view('announcement.announcement_add');
    })->name('announcement.announcement_add');

    //student route

    Route::get('/student', [StudentProfileController::class, 'studentsTable'])->name('student');

    Route::get('/student/student_add_profile', [StudentProfileController::class, 'studentAddProfile'])->name('students.student_add_profile');
    Route::post('/student/student_add_profile', [StudentProfileController::class, 'store'])->name('students.student_add_profile_post');

    Route::get('/student/hk_duty_quota', [StudentProfileController::class, 'hkQuotaIndex'])->name('students.hkDutyQuota');


    Route::get('/student/add', [StudentProfileController::class, 'index'])->name('students.student_add');
    Route::post('/student/add', [StudentProfileController::class, 'register'])->name('students.student_add_post');

    Route::post('/student/hk_duty_quota', [StudentProfileController::class, 'hkQuotaStore'])->name('students.hkDutyQuota_post');

    Route::get('/student', function () {
        return view('students.student');
    })->name('student');


    Route::get('/student_add_profile', function () {
        return view('students.student_add_profile');
    })->name('students.student_add_profile');

    Route::get('/student/hk_duty_quota', [StudentProfileController::class, 'hkQuotaIndex'])->name('students.hkDutyQuota');




    //prof route
    Route::get('/professor', function () {
        return view('professor.professor');
    })->name('professor');
    Route::get('/professor', [AdminProfessorProfileController::class, 'index'])->name('professor');

    Route::get('/professor/add', [AdminProfessorProfileController::class, 'index'])->name('professor.professor_add');
    Route::post('/professor/add', [AdminProfessorProfileController::class, 'register'])->name('professor.professor_add_post');
    Route::get('/professor/add/profile', [AdminProfessorProfileController::class, 'profAddProfileIndex'])->name('professor.professor_add_profile');
    Route::post('/professor/add/profile', [AdminProfessorProfileController::class, 'profAddProfileStore'])->name('professor.professor_add_profile_store');
    Route::get('/professor', [AdminProfessorProfileController::class, 'profTable'])->name('professor');
});
