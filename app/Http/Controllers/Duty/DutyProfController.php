<?php
namespace App\Http\Controllers\Duty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Duty;
use App\Models\StudentDutyRecord;
use Carbon\Carbon;

class DutyProfController extends Controller
{
    // Create a new duty
    public function create(Request $request)
    {
        $professor = Auth::user();

        // Validate the incoming request data
        $data = $request->validate([
            'building' => 'required|string|max:255',
            'date' => 'required|date|date_format:Y-m-d',
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
            'prof_id' => $professor->id,
            'current_scholars' => 0,
            'is_locked' => false,
            'duty_status' => 'pending',
        ]);

        return response()->json(['message' => 'Duty created successfully', 'duty' => $duty], 200);
    }

    // View all duties created by the professor
    public function index()
    {
        $professor = Auth::user();

        // Get all duties created by this professor
        $duties = Duty::where('prof_id', $professor->id)->get();

        return response()->json($duties);
    }

    // View a specific duty created by the professor
    // public function show($userId)
    // {
    //     $professor = Auth::user();
    
    //     // Check if the authenticated user has permission to view the duties of the specified user
    //     if ($professor->id != $userId) {
    //         return response()->json(['message' => 'Unauthorized access'], 403);
    //     }
    
    //     // Retrieve all duties created by the specified user
    //     $duties = Duty::where('prof_id', $userId)->get();
    
    //     if ($duties->isEmpty()) {
    //         return response()->json(['message' => 'No duties found for this user'], 404);
    //     }
    
    //     return response()->json($duties);
    // }

    public function show($dutyId)
    {
        $professor = Auth::user();

        // Get the specific duty created by the professor
        $duty = Duty::where('id', $dutyId)->where('prof_id', $professor->id)->first();

        if (!$duty) {
            return response()->json(['message' => 'Duty not found or you do not have permission to view it'], 404);
        }

        return response()->json($duty);
    }
    

    public function getRequestsForDuty($dutyId)
    {
        $professor = Auth::user();
    
        // Check if the user is authorized
        if (!$professor || $professor->role !== 'employee') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    
        // Retrieve the specific duty created by the professor
        $duty = Duty::where('id', $dutyId)
            ->where('prof_id', $professor->id)
            ->first();
    
        // Check if the duty exists and if the professor is authorized
        if (!$duty) {
            return response()->json(['message' => 'Duty not found or you do not have permission to view it'], 404);
        }
    
        // Check if the duty is completed or locked
        if ($duty->duty_status === 'completed' || $duty->is_locked) {
            return response()->json([
                'message' => 'This duty is finished or locked.',
                'duty_id' => $dutyId,
            ]);
        }
    
        $requests = StudentDutyRecord::where('duty_id', $dutyId)
            ->where('request_status', 'undecided')
            ->get();
    
        // Count the number of requests
        $requestCount = $requests->count();
    
        // Get the student IDs and names
        $studentData = $requests->map(function ($request) {
            $student = \App\Models\User::find($request->stud_id);
            return [
                'student_id' => $request->stud_id,
                'name' => $student ? $student->name : 'Unknown'
            ];
        });
    
        return response()->json([
            'duty_id' => $dutyId,
            'request_count' => $requestCount,
            'duty_details' => [
                'date' => $duty->date,
                'start_time' => $duty->start_time,
                'end_time' => $duty->end_time,
                'message' => $duty->message,
            ],
            'student_data' => $studentData
        ]);
    }
    


    public function getAcceptedStudents($dutyId)
    {
        $professor = Auth::user();

        // Get the specific duty created by the professor
        $duty = Duty::where('id', $dutyId)
            ->where('prof_id', $professor->id)
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


    // Update a specific duty
    public function update($dutyId, Request $request)
    {
        // Authenticate Professor
        $professor = Auth::user();

        // Retrieve the duty created by the professor
        $duty = Duty::where('id', $dutyId)
            ->where('prof_id', $professor->id)
            ->first();

        if (!$duty) {
            return response()->json([
                'message' => 'Duty not found or you do not have permission to update it'
            ], 404);
        }

        // Check if any student requests have been accepted for this duty
        $hasAcceptedRequests = StudentDutyRecord::where('duty_id', $dutyId)
            ->where('request_status', 'accepted')
            ->exists();

        if ($hasAcceptedRequests) {
            return response()->json([
                'message' => 'Cannot update duty details as there are accepted student requests'
            ], 400);
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
            return response()->json([
                'message' => 'Invalid time format',
                'error' => $e->getMessage()
            ], 400);
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

        // Return success response
        return response()->json([
            'message' => 'Duty updated successfully',
            'duty' => $duty
        ], 200);
    }





    // Delete a specific duty
    public function delete($dutyId)
    {
        $professor = Auth::user();

        // Get the specific duty created by the professor
        $duty = Duty::where('id', $dutyId)->where('prof_id', $professor->id)->first();

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


    // Accept a student's request to join a duty
    public function acceptStudent($dutyId, $studentId)
{
    $professor = Auth::user();

    // Get the specific duty created by the professor
    $duty = Duty::where('id', $dutyId)->where('prof_id', $professor->id)->first();

    if (!$duty) {
        return response()->json(['message' => 'Duty not found or you do not have permission to handle student requests for it'], 404);
    }

    // Find the student's request
    $studentDutyRecord = StudentDutyRecord::where('duty_id', $dutyId)
        ->where('stud_id', $studentId)
        ->first();

    if (!$studentDutyRecord) {
        return response()->json(['message' => 'Student request not found'], 404);
    }

    // Prevent changing the decision once made
    if ($studentDutyRecord->request_status !== 'undecided') {
        return response()->json(['message' => 'This request has already been ' . $studentDutyRecord->request_status . ' and cannot be changed.'], 400);
    }

    // Ensure that the duty is not over its max scholars limit
    if ($duty->current_scholars >= $duty->max_scholars) {
        return response()->json(['message' => 'Cannot accept more students, max scholars limit reached'], 400);
    }

    // Accept the student's request
    $studentDutyRecord->update(['request_status' => 'accepted']);
    $duty->increment('current_scholars');

    // Lock the duty if max scholars reached
    if ($duty->current_scholars >= $duty->max_scholars) {
        $duty->update(['is_locked' => true]);
    }

    return response()->json(['message' => 'Student accepted successfully', 'duty' => $duty]);
}


    public function rejectStudent($dutyId, $studentId)
    {
        $professor = Auth::user();

        // Get the specific duty created by the professor
        $duty = Duty::where('id', $dutyId)->where('prof_id', $professor->id)->first();

        if (!$duty) {
            return response()->json(['message' => 'Duty not found or you do not have permission to reject students for it'], 404);
        }

        // Find the student's request
        $studentDutyRecord = StudentDutyRecord::where('duty_id', $dutyId)
            ->where('stud_id', $studentId)
            ->first();

        if (!$studentDutyRecord) {
            return response()->json(['message' => 'Student request not found'], 404);
        }

        // Prevent changing the decision once made
        if ($studentDutyRecord->request_status !== 'undecided') {
            return response()->json(['message' => 'This request has already been ' . $studentDutyRecord->request_status . ' and cannot be changed.'], 400);
        }

        // Reject the student's request
        $studentDutyRecord->update(['request_status' => 'rejected']);

        return response()->json(['message' => 'Student rejected successfully']);
    }


    // Update the status of a duty
    public function updateStatus($dutyId, Request $request)
    {
        $professor = Auth::user();
    
        $duty = Duty::where('id', $dutyId)->where('prof_id', $professor->id)->first();
    
        if (!$duty) {
            return response()->json(['message' => 'Duty not found or you do not have permission to update its status'], 404);
        }
    
        // Check if the duty is already marked as completed
        if ($duty->is_completed) {
            return response()->json(['message' => 'This duty has already been completed and cannot be updated.'], 400);
        }
    
        // Validate the new status from the request
        $data = $request->validate([
            'duty_status' => 'required|in:pending,active,completed',
        ]);
    
        // Check if manually requested status is valid
        if ($data['duty_status'] === 'completed') {
            // Set status to completed and mark as completed
            $duty->update(['duty_status' => 'completed', 'is_completed' => true]);
        } else {
            // Prevent reverting to pending or active if already completed
            if ($duty->duty_status === 'completed') {
                return response()->json(['message' => 'Cannot revert a completed duty to another status.'], 400);
            }
            $duty->update(['duty_status' => $data['duty_status']]);
        }
    
        return response()->json(['message' => 'Duty status updated successfully', 'duty' => $duty]);
    }
    

    public function cancelDuty($dutyId)
    {
        $professor = Auth::user();

        // Get the specific duty created by the professor
        $duty = Duty::where('id', $dutyId)->where('prof_id', $professor->id)->first();

        if (!$duty) {
            return response()->json(['message' => 'Duty not found or you do not have permission to cancel it'], 404);
        }

        // Check if the duty is already cancelled
        if ($duty->duty_status === 'cancelled') {
            return response()->json(['message' => 'Duty is already cancelled'], 400);
        }

        // Update the duty status to cancelled
        $duty->update(['duty_status' => 'cancelled']);

        return response()->json(['message' => 'Duty cancelled successfully']);
    }




    // Lock a duty to prevent further requests
    public function lockDuty($dutyId)
{
    $professor = Auth::user();

    // Get the specific duty created by the professor
    $duty = Duty::where('id', $dutyId)->where('prof_id', $professor->id)->first();

    if (!$duty) {
        return response()->json(['message' => 'Duty not found or you do not have permission to lock it'], 404);
    }

    // Check if the duty is already locked
    if ($duty->is_locked) {
        return response()->json(['message' => 'This duty is already locked'], 400);
    }

    // Lock the duty
    $duty->update(['is_locked' => true]);

    return response()->json(['message' => 'Duty locked successfully', 'duty_id' => $duty->id]);
}

}
