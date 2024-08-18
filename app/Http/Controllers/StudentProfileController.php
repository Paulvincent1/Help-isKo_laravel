<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class StudentProfileController extends Controller
{
    public function show(){
        $user = Auth::user();
        $profile = $user->studentProfile;
        return response()->json([
            'name' => $user->name,
            'profile' => $profile
        ]);
    }

    public function store(Request $request){
        $user = Auth::user();
        if($user->studentProfile == null){

        
            $fields = $request->validate([
                'student_number' => 'required|string|max:255',
                'college' => 'required|string|max:255',
                'course' => 'required|string|max:255',
                'department' => 'required|string|max:255',
                'semester' => 'required|string|max:255',
                'birthday' => 'required|string|max:255',
                'contact_number' => 'required|string|max:255',
                'father_name' => 'required|string|max:255',
                'mother_name' => 'required|string|max:255',
                'current_address' => 'required|string|max:255',
                'permanent_address' => 'required|string|max:255',
                'profile_img' => 'image|mimes:jpg,bmp,png'
            ]);
            if($request->hasFile('profile_img')){

                $file = $request->file('profile_img');
                
                $filename = time() . '.' . $file->getClientOriginalExtension();
                
                $path = $file->storeAs('profile_img',$filename, 'public');
                
                $fields['profile_img'] = 'storage/' . $path;
                
                
            }
                
            $res = $user->studentProfile()->create($fields);

            return response($res, 201);
        } 
        return response()->json([
            'message' => 'Already have a profile'
        ], 403);

    }

    public function update(Request $request){
        $fields = $request->validate([
            'profile_img' => 'image|mimes:jpg,bmp,png',
            'contact_number' => 'string|max:255'
        ]);

        $user = Auth::user();

       
        if($request->hasFile('profile_img')){
            if(File::exists($user->studentProfile->profile_img)){
                File::delete($user->studentProfile->profile_img);
            }
            $file = $request->file('profile_img');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_img', $filename,'public');
            $fields['profile_img'] = 'storage/' . $path;
        }

        $res = $user->studentProfile()->update([
            'profile_img' => $fields['profile_img'] ?? null,
            'contact_number' => $fields['contact_number'] ?? ""

        ]);
        
        return response($res, 201);

    }
}
