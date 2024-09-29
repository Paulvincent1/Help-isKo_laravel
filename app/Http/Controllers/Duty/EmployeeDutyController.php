<?php

namespace App\Http\Controllers\Duty;

use App\Http\Controllers\Controller;
use App\Notifications\CancelledDutyNotification;
use App\Notifications\AcceptedDutyNotification;
use App\Notifications\DutyCreatedNotification;
use App\Notifications\CompletedDutyNotification;
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
        $employee->notify(new DutyCreatedNotification($duty));

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

        $duties = Duty::where('emp_id', $employee->id)->get();
        // Loop through the duties and update duty_status if is_locked is true
        foreach ($duties as $duty) {
            if ($duty->is_locked) {
                $duty->duty_status = 'active';
            }
        }

        return response()->json($duties);
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
                            'profile_img' => $request->profile_img,
                            'name' => $student ? $student->name : 'Unknown'
                        ]
                    ];
                }
            }
        }

        return response()->json($dutyDetails);
    }

    // Update a specific duty
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

        return response()->json(['message' => 'Duty deleted successfully']);
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

        // Notify the student (optional)
         \App\Models\User::find($data['stud_id'])->notify(new AcceptedDutyNotification($duty));

        // Lock the duty if max scholars limit is reached
        if ($duty->current_scholars >= $duty->max_scholars) {
            $duty->update(['is_locked' => true]);
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
         ->with('student')
         ->get()
         ->map(function ($record) {
             return [
                 'student_id' => $record->student->id,
                 'name' => $record->student->name,
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
         'duty_status' => 'required|in:active,ongoing,completed,cancelled', // Only these statuses are allowed
     ]);
 
     // Ensure that completed duties cannot be reverted to other statuses
     if ($duty->duty_status === 'completed') {
         return response()->json(['message' => 'Cannot update status of a completed duty'], 400);
     }
 
     // Use Carbon to compare current time with duty start and end times
     $currentTime = Carbon::now();
     $startTime = Carbon::parse($duty->date . ' ' . $duty->start_time);
     $endTime = Carbon::parse($duty->date . ' ' . $duty->end_time);
 
     // Automatically set status to 'ongoing' if current time is during duty time
     if ($currentTime->between($startTime, $endTime)) {
         $data['duty_status'] = 'ongoing';
     }
 
     // Automatically set status to 'completed' if the duty time has passed
     if ($currentTime->greaterThan($endTime)) {
         $data['duty_status'] = 'completed';
 
         // Notify all accepted students that the duty has been completed
         $acceptedStudents = StudentDutyRecord::where('duty_id', $duty->id)
             ->where('request_status', 'accepted')
             ->get();
 
         foreach ($acceptedStudents as $studentRecord) {
             $student = \App\Models\User::find($studentRecord->stud_id);
             if ($student) {
                 // Notify student about duty completion
                 $student->notify(new CompletedDutyNotification($duty, $student));
             }
         }
 
         // Notify the employee that the duty has been completed
         $employee->notify(new CompletedDutyNotification($duty, $employee));
     }
 
     // Check if the duty has accepted students or is already active
     $hasAcceptedStudents = StudentDutyRecord::where('duty_id', $dutyId)
         ->where('request_status', 'accepted')
         ->exists();
 
     // Ensure that status cannot be reverted to 'pending' or undone
     if ($hasAcceptedStudents || $duty->duty_status === 'active') {
         if ($data['duty_status'] === 'pending') {
             return response()->json(['message' => 'Cannot revert duty back to pending once students have been accepted or duty is active'], 400);
         }
     }
 
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
                 $student->notify(new CancelledDutyNotification($duty));
             }
         }
     }
 
     // Update the duty status
     $duty->update(['duty_status' => $data['duty_status']]);
 
     // Broadcast the real-time update to others (e.g., students)
     broadcast(new \App\Events\DutyStatusUpdated($duty))->toOthers();
 
     return response()->json(['message' => 'Duty status updated successfully', 'duty' => $duty]);
 }
 
}