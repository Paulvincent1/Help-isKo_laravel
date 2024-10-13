<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class EmployeeProfileController extends Controller
{

    public function employeeTable()
    {
        $employees = User::with('employeeProfile')->where('role', 'employee')->get();
        return view('employee.employee', ['employees' => $employees]);
    }
    public function index()
    {
        return view('employee.employee_add');
    }

    public function show(User $id)
    {
        if($id->employeeProfile == null){
            return redirect()->back();
        }
        $userProfile = $id;
        return view('employee.employee_profile', ['user' => $userProfile]);
    }

    public function edit(User $id)
    {
        $employee = $id;
    
        return view('employee.edit_employee', ['employee' => $employee]);  
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

    public function employeeAddProfileIndex()
    {
        return view('employee.employee_add_profile');
    }

    public function employeeAddProfileStore(Request $request)
    {
        $id = session('emp_id');

        $user = User::where('id', $id)->first();

        if($user == null){
            return redirect()->route('employee');
        }

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

            session()->forget('emp_id');

            return redirect()->route('employee');
        }
      
        return redirect()->back();
    }

    public function existingEmployeeAddProfileStore(Request $request, User $id)
    {
        $id = $id->id;

        $user = User::where('id', $id)->first();

        if($user == null){
            return redirect()->route('employee');
        }

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

            session()->forget('emp_id');

            return redirect()->route('employee');
        }
      
        return redirect()->back();
    }
    
    public function update(Request $request, User $id)
    {
        $id = $id->id;

        $user = User::where('id', $id)->first();

        if($user == null){
            return redirect()->route('employee');
        }

        if ($user->employeeProfile != null) {
            $fields = $request->validate([
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'birthday' => 'required|max:255',
                'contact_number' => 'required|max:255',
                'employee_number' => 'required|max:255',
                'profile_img' => 'image|mimes:jpg,bmp,png'
            ]);

            if ($request->hasFile('profile_img')) {
                if(File::exists($user->employeeProfile->profile_img)){
                    File::delete($user->employeeProfile->profile_img);
                }
                $file = $request->file('profile_img');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('profile_img', $filename, 'public');

                $fields['profile_img'] = 'storage/' . $path;
            }
            $user->employeeProfile()->update($fields);

            session()->forget('emp_id');

            return redirect()->route('employee');
        }
      
        return redirect()->back();
    }
    
}
