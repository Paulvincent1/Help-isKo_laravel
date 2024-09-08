<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HkStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentProfileController extends Controller
{
    public function studentsTable(){
        return view('students.student');
    }
    public function index(){
        return view('students.student_add');
    }

    public function register(Request $request){
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|confirmed',

        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'role' => 'student'
        ]);

        
        session(['student_id' => $user->id]);

        return redirect()->route('students.hkDutyQuota');

    }


    public function hkQuotaIndex(){
        return view('students.hkDutyQuota');
    }

    public function hkQuotaStore(Request $request){
        $fields = $request->validate([
            'duty_hours' => 'required|numeric|max_digits:80'
        ]);
        $id = session('student_id');
        $auth = User::where('id', $id)->first();

        
        if($auth->hkStatus() != null){
            $auth->hkStatus()->create([
                'remaining_hours' => $fields['duty_hours'],
                'duty_hours' => $fields['duty_hours'],
            ]);
            
        }else{
            $auth->hkStatus()->update([
                'remaining_hours' => $fields['duty_hours'],
                'duty_hours' => $fields['duty_hours'],
            ]);
        }

        return redirect()->route('students.student_add_profile');

    }

    public function studentAddProfile(){
        return view('students.student_add_profile');
    }

    public function store(Request $request){
        $id = session('student_id');
        $user = User::where('id', $id)->first();
      
        if($user->studentProfile == null){

            $fields = $request->validate([

                // student info
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'student_number' => 'required|string|max:255',
                'college' => 'required|string|max:255',
                'course' => 'required|string|max:255',
                'department' => 'required|string|max:255',
                'learning_modality' => 'required|string|max:255',
                'semester' => 'required|string|max:255',
                'birthday' => 'required|string|max:255',
                'contact_number' => 'required|string|max:255',

                //family
                'father_name' => 'required|string|max:255',
                'father_contact_number' => 'required|string|max:255',
                'mother_name' => 'required|string|max:255',
                'mother_contact_number' => 'required|string|max:255',

                //current address
                'current_address' => 'required|string|max:255',
                'current_province' => 'required|string|max:255',
                'current_country' => 'required|string|max:255',
                'current_city' => 'required|string|max:255',

                //permanent address
                'permanent_address' => 'required|string|max:255',
                'permanent_province' => 'required|string|max:255',
                'permanent_country' => 'required|string|max:255',
                'permanent_city' => 'required|string|max:255',


                // emergency person contact details
                'emergency_person_name' => 'required|string|max:255',
                'emergency_address' => 'required|string|max:255',
                'relation' => 'required|string|max:255',
                'emergency_contact_number' => 'required|string|max:255',


                'profile_img' => 'image|mimes:jpg,bmp,png'
            ]);
            if($request->hasFile('profile_img')){

                $file = $request->file('profile_img');

                $filename = time() . '.' . $file->getClientOriginalExtension();

                $path = $file->storeAs('profile_img',$filename, 'public');

                $fields['profile_img'] = 'storage/' . $path;


            }

           $user->studentProfile()->create($fields);

            return redirect()->route('student');
        }
        return redirect()->back();

    }
}
