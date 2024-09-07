<?php

use Illuminate\Support\Facades\Route;


//announcement route
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
Route::get('/student', function () {
    return view('students.student');
})->name('student');

Route::get('/student_add', function () {
    return view('students.student_add');
})->name('students.student_add');

Route::get('/student_add_profile', function () {
    return view('students.student_add_profile');
})->name('students.student_add_profile');

Route::get('/hk_duty_quota', function () {
    return view('students.hkDutyQuota');
})->name('students.hkDutyQuota');




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
