<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetToken;
use App\Models\User;
use App\Models\StudentDutyRecord; 
use App\Notifications\ForgotPassword;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\DutyStatusCountUpdated; 

class AuthController extends Controller
{
    public function register(Request $request){
// updated

        try{

            $fields = $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|confirmed',
                'role' => 'required|string|in:employee,student'
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

    public function loginEmployee(Request $request){

      
        $creds = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

    // Attempt to authenticate the user
    if (Auth::attempt($creds)) {
        $user = Auth::user();

            if($user->role == 'employee'){

                return response()->json([
                    "token" =>  $user->createToken($request->email)->plainTextToken,
                    'name' => $user->name,
                    'user' => $user->employeeProfile
                ], 200);
                
            }else{

                return response()->json(["message" => "not a employee"], 403);
            }
           
           
        }else {
            return response()->json(['message' => 'Log in failed'], 401);
        }
   
        
    }
    public function loginStud(Request $request)
    {
        $creds = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
    
        if (Auth::attempt($creds)) {
            $user = Auth::user();
    
            if ($user->role == 'student') {
                $activeDutiesCount = StudentDutyRecord::where('stud_id', $user->id)
                    ->where('request_status', 'accepted')
                    ->whereHas('duty', function ($query) {
                        $query->where('duty_status', 'active');
                    })
                    ->count();
    
                $ongoingDutiesCount = StudentDutyRecord::where('stud_id', $user->id)
                    ->where('request_status', 'accepted')
                    ->whereHas('duty', function ($query) {
                        $query->where('duty_status', 'ongoing');
                    })
                    ->count();
    
                $completedDutiesCount = StudentDutyRecord::where('stud_id', $user->id)
                    ->where('request_status', 'accepted')
                    ->whereHas('duty', function ($query) {
                        $query->where('duty_status', 'completed');
                    })
                    ->count();
    
                // Total count of duties (regardless of status)
                $totalDutiesCount = StudentDutyRecord::where('stud_id', $user->id)
                    ->where('request_status', 'accepted')
                    ->count();
    
                // Broadcast the DutyStatusCountUpdated event
                event(new DutyStatusCountUpdated($user->id, $activeDutiesCount, $ongoingDutiesCount, $completedDutiesCount, $totalDutiesCount));
    
                return response()->json([
                    "token" => $user->createToken($request->email)->plainTextToken,
                    'name' => $user->name,
                    'user' => $user->studentProfile,
                    'active_duties' => $activeDutiesCount,
                    'ongoing_duties' => $ongoingDutiesCount,
                    'completed_duties' => $completedDutiesCount,
                    'total_duties' => $totalDutiesCount,
                    'hours_to_complete' => $user->hkStatus->duty_hours,
                    'remaining_hours' => $user->hkStatus->remaining_hours
                ], 200);
            } else {
                return response()->json(["message" => "not a student"], 403);
            }
    
        } else {
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

   public function forgot(Request $request){
        $user = User::query();
        $request->validate([
            'email' => 'required|email'
        ]);
        $user = $user->where('email', $request->input('email'))->first();

        if(!$user){
            return response()->json([
                'message' => 'No record found'
            ], 404);
        }

        $resetPasswordToken = str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);

        if(!$userPassReset = PasswordResetToken::where('email', $user->email)->first()){
            PasswordResetToken::create([
                'email' => $user->email,
                'token' => $resetPasswordToken
            ]);
        }else{
            $userPassReset->update([
                'email' => $user->email,
                'token' => $resetPasswordToken
            ]);
        }

        $user->notify(new ForgotPassword($user, $resetPasswordToken));

        return response()->json([
            'message' => 'A code has been sent to your email Address.'
        ]);
        
   }

   public function resetpassword(Request $request)
   {
       $fields =  $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|confirmed',
            'token' => 'required|max:4'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if(!$user){
            return response()->json([
                'message' => 'No Record found, Incorrect email adress'
            ], 404);
        }

        $resetRequest = PasswordResetToken::where('email', $fields['email'])->first();

        if(!$resetRequest || $resetRequest->token != $fields['token']){
            return response()->json([
                'message' => ' token mismatch'
            ], 400);
        }

        $user->update([
            'password' => bcrypt($fields['password'])
        ]);

        $user->tokens()->delete();


        return response()->json([
            'message' => 'Reset password successfully'
        ], 200);



   }
}
