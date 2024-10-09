<?php


use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\EmployeeProfileController;
use App\Http\Controllers\Admin\EmployeeProfileController as AdminEmployeeProfileController;
use App\Http\Controllers\Admin\StudentProfileController;
use App\Http\Controllers\Admin\AdminRenewalFormController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Middleware\Admin\isAdmin;
use Illuminate\Support\Facades\Route;


Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AdminAuthController::class, 'index'])->name('login');
    Route::post('/login-admin', [AdminAuthController::class, 'login'])->name('loginAdmin');
});



Route::middleware([isAdmin::class, 'auth'])->group(function () {

    Route::get('/', [AdminController::class, 'index'])->name('index');
    
    Route::get('/announcement',[AnnouncementController::class, 'index'])->name('announcement');
    Route::get('/announcement/add', [AnnouncementController::class, 'create'])->name('announcement.add');
    Route::post('/announcement/add', [AnnouncementController::class, 'store'])->name('announcement.post');
    Route::delete('/announcement/delete/{id}', [AnnouncementController::class, 'delete'])->name('announcement.delete');

    //student route

    Route::get('/student', [StudentProfileController::class, 'studentsTable'])->name('student');
    Route::get('/student/profile/{id}',[StudentProfileController::class, 'show'])->name('student.viewProfile');

    Route::get('/student/student_add_profile', [StudentProfileController::class, 'studentAddProfile'])->name('students.student_add_profile');
    Route::post('/student/student_add_profile', [StudentProfileController::class, 'store'])->name('students.student_add_profile_post');

    Route::get('/student/hk_duty_quota', [StudentProfileController::class, 'hkQuotaIndex'])->name('students.hkDutyQuota');


    Route::get('/student/add', [StudentProfileController::class, 'index'])->name('students.student_add');
    Route::post('/student/add', [StudentProfileController::class, 'register'])->name('students.student_add_post');

    Route::post('/student/hk_duty_quota', [StudentProfileController::class, 'hkQuotaStore'])->name('students.hkDutyQuota_post');

    Route::get('/student_add_profile', function () {
        return view('students.student_add_profile');
    })->name('students.student_add_profile');

    Route::get('/student/hk_duty_quota', [StudentProfileController::class, 'hkQuotaIndex'])->name('students.hkDutyQuota');

    //renewal form
        // temporary route 
        Route::get('/renewal', [AdminRenewalFormController::class, 'index'])->name('renewal');

        Route::get('/admin/renewal-forms', [AdminRenewalFormController::class, 'index'])->name('admin.renewal_forms.index');
        Route::get('/admin/renewal-form/{id}', [AdminRenewalFormController::class, 'show'])->name('admin.renewal_forms.show');
        Route::put('/admin/renewal-form/{id}/status', [AdminRenewalFormController::class, 'updateStatus'])->name('admin.renewal_forms.updateStatus');
        Route::delete('/admin/renewal-form/{id}', [AdminRenewalFormController::class, 'destroy'])->name('admin.renewal_forms.destroy');



    //prof route
    Route::get('/employee', [EmployeeProfileController::class, 'index'])->name('employee');
    Route::get('/employee/profile/{id}', [EmployeeProfileController::class, 'show'])->name('employee.viewProfile');

    Route::get('/employee/add', [EmployeeProfileController::class, 'index'])->name('employee.employee_add');
    Route::post('/employee/add', [EmployeeProfileController::class, 'register'])->name('employee.employee_add_post');
    Route::get('/employee/add/profile', [EmployeeProfileController::class, 'employeeAddProfileIndex'])->name('employee.employee_add_profile');
    Route::post('/employee/add/profile', [EmployeeProfileController::class, 'employeeAddProfileStore'])->name('employee.employee_add_profile_store');
    Route::get('/employee', [EmployeeProfileController::class, 'employeeTable'])->name('employee');
});
