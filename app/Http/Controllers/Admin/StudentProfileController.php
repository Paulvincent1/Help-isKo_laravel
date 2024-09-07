<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StudentProfileController extends Controller
{

    public function index(){
        return view('students.student_add');
    }

    public function register(Request $request){
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|confirmed',

        ]);

        User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
        ]);

        return redirect()->route('students.hkDutyQuota');

    }


    public function hkQuotaIndex(){
        return view('students.hkDutyQuota');
    }

    public function hkQuotaStore(){

    }
}
