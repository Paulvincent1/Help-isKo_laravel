<?php


use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\EmployeeProfileController;
use App\Http\Controllers\Admin\EmployeeProfileController as AdminEmployeeProfileController;
use App\Http\Controllers\Admin\StudentProfileController;
use App\Http\Controllers\Admin\AdminRenewalFormController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\DutyController;
use App\Http\Middleware\Admin\isAdmin;
use Illuminate\Support\Facades\Route;


Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AdminAuthController::class, 'index'])->name('login');
    Route::post('/login-admin', [AdminAuthController::class, 'login'])->name('loginAdmin');
});



Route::middleware([isAdmin::class, 'auth'])->group(function () {
    Route::post('/logout-admin', [AdminAuthController::class, 'logout'])->name('logoutAdmin');

    Route::get('/', [AdminController::class, 'index'])->name('index');
    
    Route::get('/announcement',[AnnouncementController::class, 'index'])->name('announcement');
    Route::get('/announcement/add', [AnnouncementController::class, 'create'])->name('announcement.add');
    Route::post('/announcement/add', [AnnouncementController::class, 'store'])->name('announcement.post');
    Route::delete('/announcement/delete/{id}', [AnnouncementController::class, 'delete'])->name('announcement.delete');

    //student route

    Route::get('/student', [StudentProfileController::class, 'studentsTable'])->name('student');
    Route::get('/student/profile/{id}',[StudentProfileController::class, 'show'])->name('student.viewProfile');
    Route::get('/student/profile/{id}/edit', [StudentProfileController::class, 'edit'])->name('student.edit');
    Route::put('/student/profile/{id}/update', [StudentProfileController::class, 'update'])->name('student.update');

    Route::get('/student/add/profile', [StudentProfileController::class, 'studentAddProfile'])->name('students.student_add_profile');
    Route::post('/student/add/profile', [StudentProfileController::class, 'store'])->name('students.student_add_profile_post');
    Route::get('/student/add/profile/{id}/existing', [StudentProfileController::class, 'existingStudentAddProfile'])->name('students.existing_student_add_profile');
    Route::post('/student/add/profile/{id}/existing', [StudentProfileController::class, 'existingStudentProfileStore'])->name('students.existing_student_add_profile_post_store');

    Route::get('/student/hk_duty_quota', [StudentProfileController::class, 'hkQuotaIndex'])->name('students.hkDutyQuota');
    Route::get('/student/hk_duty_quota/{id}', [StudentProfileController::class, 'hkQuotaIndexExisting'])->name('students.hkDutyQuotaExisting');
    Route::post('/student/hk_duty_quota/{id}', [StudentProfileController::class, 'hkQuotaStoreExisting'])->name('students.hkDutyQuotaExistingStore');


    Route::get('/student/add', [StudentProfileController::class, 'index'])->name('students.student_add');
    Route::post('/student/add', [StudentProfileController::class, 'register'])->name('students.student_add_post');

    Route::post('/student/hk_duty_quota', [StudentProfileController::class, 'hkQuotaStore'])->name('students.hkDutyQuota_post');

    Route::get('/student_add_profile', function () {
        return view('students.student_add_profile');
    })->name('students.student_add_profile');

    Route::get('/student/hk_duty_quota', [StudentProfileController::class, 'hkQuotaIndex'])->name('students.hkDutyQuota');

    //renewal form
    Route::get('/renewal', [AdminRenewalFormController::class, 'index'])->name('renewal');

    // Show a specific renewal request's details
    Route::get('/renewal/{id}', [AdminRenewalFormController::class, 'show'])->name('renewal.show');
    
    // Update status of a renewal request
    Route::put('/renewal/{id}/update', [AdminRenewalFormController::class, 'updateRenewal'])->name('renewal.updateRenewal');
    
    // Delete a renewal request
    Route::delete('/renewal/{id}/delete', [AdminRenewalFormController::class, 'deleteRenewal'])->name('renewal.deleteRenewal');
    

    // duty route
    Route::get('/duty', [DutyController::class, 'index'])->name('duty');
    Route::get('/duty/export', [DutyController::class, 'export'])->name('duty.export');


    //prof route
    Route::get('/employee', [EmployeeProfileController::class, 'index'])->name('employee');
    Route::get('/employee/profile/{id}', [EmployeeProfileController::class, 'show'])->name('employee.viewProfile');
    Route::get('/employee/profile/{id}/edit', [EmployeeProfileController::class, 'edit'])->name('employee.edit');
    Route::put('/employee/profile/{id}/update', [EmployeeProfileController::class, 'update'])->name('employee.update');

    Route::get('/employee/add', [EmployeeProfileController::class, 'index'])->name('employee.employee_add');
    Route::post('/employee/add', [EmployeeProfileController::class, 'register'])->name('employee.employee_add_post');
    Route::get('/employee/add/profile', [EmployeeProfileController::class, 'employeeAddProfileIndex'])->name('employee.employee_add_profile');
    Route::get('/employee/add/profile/{id}/existing', [EmployeeProfileController::class, 'employeeAddProfileIndex'])->name('employee.existing_employee_add_profile');
    Route::post('/employee/add/profile/{id}/existing', [EmployeeProfileController::class, 'existingEmployeeAddProfileStore'])->name('employee.existing_employee_add_profile_store');
    Route::post('/employee/add/profile', [EmployeeProfileController::class, 'employeeAddProfileStore'])->name('employee.employee_add_profile_store');
    Route::get('/employee', [EmployeeProfileController::class, 'employeeTable'])->name('employee');
});
