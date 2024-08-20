<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request){
// updated

        try{

            $fields = $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|confirmed',
                'role' => 'required|string|in:professor,student'
            ]);
            $user = User::create([
                'name' => $fields['name'],
                'email' => $fields['email'],
                'password' => bcrypt($fields['password']),
                'role' => $fields['role']
            ]);
            
            return response($user, 201);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function loginProf(Request $request){

      
        $creds = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if(Auth::attempt($creds)){

            $user = Auth::user();

            if($user->role == 'professor'){

                return response()->json([
                    "token" =>  $user->createToken($request->email)->plainTextToken,
                    'name' => $user->name,
                    'user' => $user->studentProfile
                ], 200);
                
            }else{

                return response()->json(["message" => "not a professor"]);
            }
           
           
        }else {
            return response()->json(['message' => 'Log in failed'], 500);
        }
   
        
    }
    public function loginStud(Request $request){

        $creds = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if(Auth::attempt($creds)){

            $user = Auth::user();

            if($user->role == 'student'){

                return response()->json([
                    "token" =>  $user->createToken($request->email)->plainTextToken,
                    'name' => $user->name,
                    'user' => $user->studentProfile
                ], 200);
                
            }else{

                return response()->json(["message" => "not a student"]);
            }
           
           
        }else {
            return response()->json(['message' => 'Log in failed'], 500);
        }
       
        
    }

    public function logout(Request $request)
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        $user->tokens()->delete();

        return response()->json(['message' => 'Logged Out'], 200);
    }
  //d sd paul
}
