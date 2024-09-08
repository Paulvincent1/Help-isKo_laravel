<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeProfileController extends Controller
{

    public function profTable()
    {
        return view('employee.employee');
    }
    public function index()
    {
        return view('employee.employee_add');
    }

    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|confirmed',
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'role' => 'employee'
        ]);

        session(['emp_id' => $user->id]);

        return redirect()->route('employee.employee_add_profile');
    }

    public function profAddProfileIndex()
    {
        return view('employee.employee_add_profile');
    }

    public function profAddProfileStore(Request $request)
    {
        $id = session('emp_id');
        $user = User::where('id', $id)->first();

        if ($user->employeeProfile == null) {
            $fields = $request->validate([
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'birthday' => 'required|max:255',
                'contact_number' => 'required|max:255',
                'employee_number' => 'required|max:255',
                'profile_img' => 'image|mimes:jpg,bmp,png'
            ]);

            if ($request->hasFile('profile_img')) {
                $file = $request->file('profile_img');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('profile_img', $filename, 'public');

                $fields['profile_img'] = 'storage/' . $path;
            }
            $user->employeeProfile()->create($fields);
            return redirect()->route('professor');
        }
        return redirect()->back();
    }
}
