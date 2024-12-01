<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class EmployeeProfileController extends Controller
{
    public function index(){
        $user = Auth::user();
       

        return response()->json([
            'employee' =>  $user->employeeProfile
        ],200 );
    }
    public function show(User $id){
        
        return response()->json([
            'name' => $id->name,
            'employee' => $id->employeeProfile
        ]);
    }
    public function create(Request $request){

        $user = Auth::user();
        $fields = $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'birthday' => 'required|max:255',
            'contact_number' => 'required|max:255',
            'employee_number' => 'required|max:255',
            'profile_img' => 'image|mimes:jpg,bmp,png'
        ]);

        if($request->hasFile('profile_img')){
            $file = $request->file('profile_img');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_img', $filename,'public');

            $fields['profile_img'] = 'storage/' . $path;
        }

        $res = $user->employeeProfile()->create($fields);
        return response($res, 201);
    }

    public function update(Request $request){
        $user = Auth::user();
        $fields = $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'birthday' => 'required|max:255',
            'contact_number' => 'required|max:255',
            'employee_number' => 'required|max:255',
            'profile_img' => 'image|mimes:jpg,bmp,png',
        ]);

        if($request->hasFile('profile_img')){
            if(File::exists($user->employeeProfile->profile_img)){
                File::delete($user->employeeProfile->profile_img);
            }
            else{

                $file = $request->file('profile_img');
                $filename = time() . '.' . $file->getClientOriginalExtension();

                $path = $file->storeAs('profile_img', $filename, 'public');

                $fields['profile_img'] = '/storage' . $path;
            }
        }

        $res = $user->employeeProfile()->create($fields);
        
        return response($res, 201);
    }

   
}
