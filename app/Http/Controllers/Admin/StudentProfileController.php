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
        $students = User::with('employeeProfile')->where('role', 'student')->get();
        return view('students.student', ['students' => $students]);
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

    public function show(User $id){
        if($id->studentProfile == null){
            return redirect()->back();
        }
        $userProfile = $id;

        $dutyCompleted = $id->notifications()->where('type', 'App\Notifications\Admin\StudentCompletedDutyNotification')->get();

        $hkStatus = $id->hkStatus;
        if($hkStatus == null){
            return redirect()->route('student');
        }

        
        return view('students.student_profile', ['user' => $userProfile, 'dutyCompleted' => $dutyCompleted, 'hkStatus' => $hkStatus]);

    }


    public function hkQuotaIndex(){
        return view('students.hkDutyQuota');
    }
    public function hkQuotaIndexExisting(User $id){
        return view('students.hkDutyQuota');
    }

    public function hkQuotaStore(Request $request){
        $id = session('student_id');
        $user = User::where('id', $id)->first();

        if($user == null){
            return redirect()->route('student');
        }
        
        $fields = $request->validate([
            'duty_hours' => 'required|numeric|max:300|min:50'
        ]);

        if($user->hkStatus == null){
            $user->hkStatus()->create([
                'remaining_hours' => $fields['duty_hours'],
                'duty_hours' => $fields['duty_hours'],
            ]);
        }else{
            $user->hkStatus()->update([
                'remaining_hours' => $fields['duty_hours'],
                'duty_hours' => $fields['duty_hours'],
            ]);
        }
        if($user->studentProfile != null){
            return redirect()->route('student');
        }else{
            return redirect()->route('students.student_add_profile');
        }

    }
    public function hkQuotaStoreExisting(Request $request, User $id){
        $id = $id->id;
        $user = User::where('id', $id)->first();

        if($user == null){
            return redirect()->route('student');
        }
        $fields = $request->validate([
            'duty_hours' => 'required|numeric|max:300|min:50'
        ]);
        
        if($user->hkStatus == null){
            $user->hkStatus()->create([
                'remaining_hours' => $fields['duty_hours'],
                'duty_hours' => $fields['duty_hours'],
            ]);
        }else{
            $user->hkStatus()->update([
                'remaining_hours' => $fields['duty_hours'],
                'duty_hours' => $fields['duty_hours'],
            ]);
        }
        if($user->studentProfile != null){
            return redirect()->route('student');
        }else{
            
            return redirect()->route('students.existing_student_add_profile',['id' => $id]);
        }

    }

    public function studentAddProfile(){
        return view('students.student_add_profile');
    }
    public function existingStudentAddProfile(User $id){
        if($id->hkStatus == null){
            return redirect()->route('students.hkDutyQuotaExisting',['id' => $id->id]);
        }
        return view('students.student_add_profile');
    }
    public function edit(User $id){
        if($id->studentProfile == null){
            
            return redirect()->back();
        }
        $userProfile = $id;
        return view('students.edit_student' ,['student' => $userProfile]);
    }

    public function store(Request $request)
    {
        $id = session('student_id');
        $user = User::where('id', $id)->first();

        if($user == null){
            session()->forget('student_id');
            return redirect()->route('student');
        }
      
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

            session()->forget('student_id');

            return redirect()->route('student');
        }

        return redirect()->back();

    }
    public function existingStudentProfileStore(Request $request, User $id)
    {
        $id = $id->id;
        $user = User::where('id', $id)->first();

        if($user == null){
            session()->forget('student_id');
            return redirect()->route('student');
        }
      
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

            session()->forget('student_id');

            return redirect()->route('student');
        }

        return redirect()->back();

    }
    public function update(Request $request, User $id)
    {
        $id = $id->id;
        $user = User::where('id', $id)->first();

        if($user == null){
      
            session()->forget('student_id');
            return redirect()->route('student');
        }
      
        if($user->studentProfile != null){

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

            $user->studentProfile()->update($fields);

            session()->forget('student_id');

            return redirect()->route('student');
        }

        return redirect()->back();

    }
}
