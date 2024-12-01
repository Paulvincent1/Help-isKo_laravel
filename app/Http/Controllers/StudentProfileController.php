<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\StudentDutyRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Events\DutyStatusCountUpdated;

class StudentProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $profile = $user->studentProfile;
    
        // Get the count of active, ongoing, completed, and total duties where the student is accepted
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
    
        // Fire the event to notify of updated duty status counts
        event(new DutyStatusCountUpdated($user->id, $activeDutiesCount, $ongoingDutiesCount, $completedDutiesCount, $totalDutiesCount));
    
        return response()->json([
            'name' => $user->name,
            'profile' => $profile,
            'active_duties' => $activeDutiesCount ?? 0,
            'ongoing_duties' => $ongoingDutiesCount ?? 0,
            'completed_duties' => $completedDutiesCount ?? 0,
            'total_duties' => $totalDutiesCount ?? 0,
        ]);
    }
    

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->studentProfile == null) {
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

                // family
                'father_name' => 'required|string|max:255',
                'father_contact_number' => 'required|string|max:255',
                'mother_name' => 'required|string|max:255',
                'mother_contact_number' => 'required|string|max:255',

                // current address
                'current_address' => 'required|string|max:255',
                'current_province' => 'required|string|max:255',
                'current_country' => 'required|string|max:255',
                'current_city' => 'required|string|max:255',

                // permanent address
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

            if ($request->hasFile('profile_img')) {
                $file = $request->file('profile_img');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('profile_img', $filename, 'public');
                $fields['profile_img'] = 'storage/' . $path;
            }

            $res = $user->studentProfile()->create($fields);
            return response($res, 201);
        }

        return response()->json([
            'message' => 'Already have a profile'
        ], 403);
    }

    public function update(Request $request)
    {
        $fields = $request->validate([
            'profile_img' => 'image|mimes:jpg,bmp,png',
            'contact_number' => 'string|max:255'
        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_img')) {
            if (File::exists($user->studentProfile->profile_img)) {
                File::delete($user->studentProfile->profile_img);
            }
            $file = $request->file('profile_img');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_img', $filename, 'public');
            $fields['profile_img'] = 'storage/' . $path;
        }

        $res = $user->studentProfile()->update([
            'profile_img' => $fields['profile_img'] ?? null,
            'contact_number' => $fields['contact_number'] ?? ""
        ]);

        return response($res, 201);
    }
}
