<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    

    public function index()
    {
        $employees = User::with('employeeProfile')->where('role', 'employee')->get();

        $students = User::with('studentProfile')->where('role', 'student')->get();


        return view('index',['students' => $students, 'employees' => $employees]);
    }
}
