<?php

namespace App\Http\Controllers\Duty;

use App\Http\Controllers\Controller;
use App\Notifications\DutyNotifications\UnfinishedDutyNotification;
use App\Notifications\DutyNotifications\CompletedDutyNotification;
use App\Notifications\DutyNotifications\OngoingDutyNotification;
use App\Notifications\DutyNotifications\ActiveDutyNotification;
use App\Notifications\DutyNotifications\AcceptedDutyNotification;
use App\Notifications\DutyRecentActivities\DutyPostedNotification;
use App\Notifications\DutyRecentActivities\DutyEditedNotification;
use App\Notifications\DutyRecentActivities\DutyRemovedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Duty;
use App\Models\StudentDutyRecord;
use Carbon\Carbon;


class EmployeeDutyController extends Controller
{
    // Create a new duty

    public function create(Request $request)
    {
        $employee = Auth::user();

        // Validate the incoming request data
        $data = $request->validate([
            'building' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'message' => 'nullable|string',
            'max_scholars' => 'required|integer|min:1',
        ]);

        // Calculate the duration in minutes
        $startTime = Carbon::createFromFormat('H:i', $data['start_time']);
        $endTime = Carbon::createFromFormat('H:i', $data['end_time']);
        $duration = $startTime->diffInMinutes($endTime);

        // Create the duty
        $duty = Duty::create([
            'building' => $data['building'],
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'duration' => $duration,
            'message' => $data['message'],
            'max_scholars' => $data['max_scholars'],
            'emp_id' => $employee->id,
            'current_scholars' => 0,
            'is_locked' => false,
            'duty_status' => 'pending',
        ]);

        // Trigger notification for the employee
        $employee->notify(new DutyPostedNotification($duty));

        return response()->json(['message' => 'Duty created successfully', 'duty' => $duty], 201);
    }


    public function index()
    {
        // Get the authenticated employee
        $employee = Auth::user();

        // Ensure the authenticated user is an employee
        if (!$employee || $employee->role !== 'employee') {
            return response()->json(['message' => 'Unauthorized or invalid user role'], 403);
        }

        // Get the duties for the authenticated employee
        $duties = Duty::where('emp_id', $employee->id)
        ->get();

        // Prepare the response data
        $response = [];

        foreach ($duties as $duty) {
            // Update duty_status if is_locked is true
            if ($duty->is_locked) {
                $duty->duty_status = 'active';
            }

            // Get the accepted students for this duty
            $acceptedStudents = StudentDutyRecord::where('duty_id', $duty->id)
                ->where('request_status', 'accepted')
                ->with('student.studentProfile')
                ->get()
                ->map(function ($record) {
                    return [
                        'student_id' => $record->student->id,
                        'name' => $record->student->name,
                        'email' => $record->student->email,
                        'student_number' => $record->student->studentProfile->student_number,
                        'contact_number' => $record->student->studentProfile->contact_number,
                        'semester' => $record->student->studentProfile->semester,
                        'course' => $record->student->studentProfile->course,
                        'request_status' => $record->request_status,
                        'profile_image' => $record->student->studentProfile->profile_img
                    ];
                });

            // Add the raw duty and accepted students to the response
            $response[] = [
                'duty' => $duty, // Return the entire duty object
                'profile_img' => $employee->employeeProfile->profile_img,
                'accepted_students' => $acceptedStudents,
            ];
        }

        return response()->json($response);
    }

    public function show($dutyId)
{
    $employee = Auth::user();

    // Find the specific duty created by the employee
    $duty = Duty::where('id', $dutyId)
                ->where('emp_id', $employee->id)
                ->first();

    if (!$duty) {
        return response()->json(['message' => 'Duty not found or unauthorized'], 404);
    }

    return response()->json($duty);
}


    // Retrieve all pending requests for duties created by the employee
    public function getRequestsForAllDuties()
    {
        // Get the authenticated employee
        $employee = Auth::user();

        // Check if the user is an employee
        if (!$employee || $employee->role !== 'employee') {
            return response()->json(['message' => 'Unauthorized access'], 403);
        }

        // Retrieve all duties created by the authenticated employee
        $duties = Duty::where('emp_id', $employee->id)
            ->where('duty_status', 'pending')
            ->where('is_locked', false)
            ->get();

        // Prepare an array to store duty details
        $dutyDetails = [];

        // Process each duty to get details and associated undecided requests
        foreach ($duties as $duty) {
            // Get requests for the current duty
            $requests = StudentDutyRecord::where('duty_id', $duty->id)
                ->where('request_status', 'undecided')
                ->get();
            // Only add the duty details if there are requests
            if (!$requests->isEmpty()) {
                // Compile student data for each request
                foreach ($requests as $request) {
                    $student = \App\Models\User::find($request->stud_id);
                    $studentProfile = $student ? $student->studentProfile : null;
                    $dutyDetails[] = [
                        'duty_id' => $duty->id,
                        'building' => $duty->building,
                        'start_time' => $duty->start_time,
                        'end_time' => $duty->end_time,
                        'date' => $duty->date,
                        'message' => $duty->message,
                        'current_scholars' => $duty->current_scholars,
                        'max_scholars' => $duty->max_scholars,
                        'request_count' => $requests->count(), // Total requests count
                        'student_data' => [
                            'student_id' => $request->stud_id,
                            'email' => $student->email,
                            'name' => $student ? $student->name : 'Unknown',
                            'last_name' => $studentProfile ? $studentProfile->last_name : null,
                            'contact_number' => $studentProfile ? $studentProfile->contact_number : null,
                            'student_number' => $studentProfile ? $studentProfile->student_number : null,
                            'course' => $studentProfile ? $studentProfile->course : null,
                            'semester' => $studentProfile ? $studentProfile->semester : null,
                            'profile_image' => $studentProfile ? $studentProfile->profile_img : null
                        ]
                    ];
                }
            }
        }

        return response()->json($dutyDetails);
    }

    // Update a specific duty, 
    public function update($dutyId, Request $request)
    {
        $employee = Auth::user();

        // Retrieve the duty created by the employee
        $duty = Duty::where('id', $dutyId)
            ->where('emp_id', $employee->id)
            ->first();

        if (!$duty) {
            return response()->json(['message' => 'Duty not found or you do not have permission to update it'], 404);
        }

        // Validate the incoming request data
        $validatedData = $request->validate([
            'building' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'message' => 'nullable|string',
            'max_scholars' => 'required|integer|min:1',
        ]);

        // Calculate the duration in minutes
        try {
            $startTime = Carbon::createFromFormat('H:i', $validatedData['start_time']);
            $endTime = Carbon::createFromFormat('H:i', $validatedData['end_time']);
            $duration = $startTime->diffInMinutes($endTime);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid time format', 'error' => $e->getMessage()], 400);
        }

        // Update the duty
        $duty->update([
            'building' => $validatedData['building'],
            'date' => $validatedData['date'],
            'start_time' => $validatedData['start_time'],
            'end_time' => $validatedData['end_time'],
            'duration' => $duration,
            'message' => $validatedData['message'],
            'max_scholars' => $validatedData['max_scholars'],
        ]);

       // Triggers the notification for the recent activitiy
        $employee->notify(new DutyEditedNotification($duty));
        return response()->json(['message' => 'Duty updated successfully', 'duty' => $duty], 200);
    }

    // Delete a specific duty 
    public function delete($dutyId)
    {
        $employee = Auth::user();
    
        // Get the specific duty created by the employee
        $duty = Duty::where('id', $dutyId)
            ->where('emp_id', $employee->id)
            ->first();
    
        // If the duty is not found, return a 404 response
        if (!$duty) {
            return response()->json(['message' => 'Duty not found or you do not have permission to delete it'], 404);
        }
    
        // Check if any requests have been accepted for this duty
        $hasAcceptedRequests = StudentDutyRecord::where('duty_id', $dutyId)
            ->where('request_status', 'accepted')
            ->exists();
    
        if ($hasAcceptedRequests) {
            return response()->json(['message' => 'Cannot delete duty as student requests have already been accepted'], 400);
        }
    
        // Delete the duty
        $duty->delete();
    
        $employee->notify(new DutyRemovedNotification($duty));
    
        return response()->json(['message' => 'Duty deleted successfully'], 200);
    }
    

    public function acceptStudent(Request $request)
{
    // Validate the incoming request
    $data = $request->validate([
        'duty_id' => 'required|integer',
        'stud_id' => 'required|integer',
    ]);

    // Get the authenticated employee
    $employee = Auth::user();

    // Find the duty created by the employee
    $duty = Duty::where('id', $data['duty_id'])
        ->where('emp_id', $employee->id)
        ->first();

    if (!$duty) {
        return response()->json(['message' => 'Duty not found or you do not have permission to handle student requests for it'], 404);
    }

    // Find the student's duty request
    $studentDutyRecord = StudentDutyRecord::where('duty_id', $duty->id)
        ->where('stud_id', $data['stud_id'])
        ->where('request_status', 'undecided')
        ->first();

    if (!$studentDutyRecord) {
        return response()->json(['message' => 'Student request not found or already decided'], 404);
    }

    // Ensure the duty is not over its max scholars limit
    if ($duty->current_scholars >= $duty->max_scholars) {
        return response()->json(['message' => 'Cannot accept more students, max scholars limit reached'], 400);
    }

    // Accept the student's request
    $studentDutyRecord->update(['request_status' => 'accepted']);
    $duty->increment('current_scholars');

    // Notify the student
    \App\Models\User::find($data['stud_id'])->notify(new AcceptedDutyNotification($duty));

    // Lock the duty if max scholars limit is reached and notify the employee
    if ($duty->current_scholars >= $duty->max_scholars) {
        $duty->update(['is_locked' => true]);

        // Notify the employee that the duty is now active
        $employee->notify(new ActiveDutyNotification($duty, $employee));
    }

    return response()->json(['message' => 'Student accepted successfully', 'duty' => $duty], 200);
}



    public function rejectStudent(Request $request)
    {
        // Validate the incoming request
        $data = $request->validate([
            'duty_id' => 'required|integer',
            'stud_id' => 'required|integer',
        ]);

        // Get the authenticated employee
        $employee = Auth::user();

        // Find the duty created by the employee
        $duty = Duty::where('id', $data['duty_id'])
            ->where('emp_id', $employee->id)
            ->first();

        if (!$duty) {
            return response()->json(['message' => 'Duty not found or you do not have permission to handle student requests for it'], 404);
        }

        // Find the student's duty request
        $studentDutyRecord = StudentDutyRecord::where('duty_id', $duty->id)
            ->where('stud_id', $data['stud_id'])
            ->where('request_status', 'undecided')
            ->first();

        if (!$studentDutyRecord) {
            return response()->json(['message' => 'Student request not found or already decided'], 404);
        }

        // Reject the student's request (Soft delete if SoftDeletes trait is used)
        $studentDutyRecord->delete();

        // Optionally log the rejection
        \Log::info('Student request rejected', [
            'employee_id' => $employee->id,
            'student_id' => $data['stud_id'],
            'duty_id' => $duty->id,
            'rejected_at' => now(),
        ]);

        return response()->json(['message' => 'Student request rejected successfully'], 200);
    }

 // Get accepted students for a specific duty
 public function getAcceptedStudents($dutyId)
 {
     $employee = Auth::user();

     // Get the specific duty created by the employee
     $duty = Duty::where('id', $dutyId)
         ->where('emp_id', $employee->id)
         ->first();

     if (!$duty) {
         return response()->json(['message' => 'Duty not found or you do not have permission to view it'], 404);
     }

     // Get the students who have been accepted for this duty
     $acceptedStudents = StudentDutyRecord::where('duty_id', $dutyId)
         ->where('request_status', 'accepted')
         ->with('student.studentProfile')
         ->get()
         ->map(function ($record) {
             return [
                 'student_id' => $record->student->id,
                 'name' => $record->student->name,
                 'course' => $record->student->studentProfile->course,
                 'student_number' => $record->student->studentProfile->student_number,
                 'request_status' => $record->request_status,
             ];
         });

     return response()->json([
         'duty_id' => $duty->id,
         'duty_status' => $duty->duty_status,
         'accepted_students' => $acceptedStudents,
     ]);
 }
 public function getAcceptedStudentNames()
{
    $employee = Auth::user();

    // Get all duties created by the employee
    $duties = Duty::where('emp_id', $employee->id)->pluck('id');

    if ($duties->isEmpty()) {
        return response()->json(['message' => 'No duties found for this employee'], 404);
    }

    // Get all students who have been accepted into the employee's duties
    $acceptedStudents = StudentDutyRecord::whereIn('duty_id', $duties)
        ->where('request_status', 'accepted')
        ->with('student') //  a relation in the StudentDutyRecord model to fetch the student details
        ->get()
        ->map(function ($record) {
            return [
                'student_name' => $record->student->name,
                'course' => $record->student->course,  
                'rating' => $record->student->rating,  
                'duty_id' => $record->duty_id,  
                'duty_status' => $record->duty->duty_status,  
            ];
        });

    return response()->json($acceptedStudents);
}


public function updateStatus($dutyId, Request $request)
{
    $employee = Auth::user();

    // Find the duty created by the employee
    $duty = Duty::where('id', $dutyId)->where('emp_id', $employee->id)->first();

    if (!$duty) {
        return response()->json(['message' => 'Duty not found or unauthorized'], 404);
    }

    // Validate the incoming request
    $data = $request->validate([
        'duty_status' => 'required|in:cancelled', // Only cancellation is manually handled
    ]);

    // Handle cancellation specifically
    if ($data['duty_status'] === 'cancelled') {
        // Check if the duty is already cancelled
        if ($duty->duty_status === 'cancelled') {
            return response()->json(['message' => 'Duty is already cancelled'], 400);
        }

        // Notify accepted students that the duty has been cancelled
        $acceptedStudents = StudentDutyRecord::where('duty_id', $dutyId)
            ->where('request_status', 'accepted')
            ->get();

        foreach ($acceptedStudents as $studentRecord) {
            $student = \App\Models\User::find($studentRecord->stud_id);
            if ($student) {
                $student->notify(new UnfinishedDutyNotification($duty));
            }
        }
    }

    // Let the model handle dynamic updates for 'ongoing', 'active', and 'completed' statuses
    $currentDutyStatus = $duty->duty_status;

    // If the current status is "ongoing", notify users about the ongoing duty
    if ($currentDutyStatus === 'ongoing') {
        $acceptedStudents = StudentDutyRecord::where('duty_id', $duty->id)
            ->where('request_status', 'accepted')
            ->get();

        foreach ($acceptedStudents as $studentRecord) {
            $student = \App\Models\User::find($studentRecord->stud_id);
            if ($student) {
                $student->notify(new OngoingDutyNotification($duty));
            }
        }
    }

    // Automatically notify if the duty is completed
    if ($currentDutyStatus === 'completed') {
        $acceptedStudents = StudentDutyRecord::where('duty_id', $duty->id)
            ->where('request_status', 'accepted')
            ->get();

        foreach ($acceptedStudents as $studentRecord) {
            $student = \App\Models\User::find($studentRecord->stud_id);
            if ($student) {
                $student->notify(new CompletedDutyNotification($duty, $student));
            }
        }

        $employee->notify(new CompletedDutyNotification($duty, $employee));
    }

    // Update the duty status only for cancellation
    if ($data['duty_status'] === 'cancelled') {
        $duty->update(['duty_status' => 'cancelled']);
    }

    return response()->json(['message' => 'Duty status updated successfully', 'duty' => $duty]);
}
}