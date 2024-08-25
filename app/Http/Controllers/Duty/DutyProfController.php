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
            'prof_id' => $professor->id,
            'current_scholars' => 0,
            'is_locked' => false,
            'duty_status' => 'pending',
        ]);

        return response()->json(['message' => 'Duty created successfully', 'duty' => $duty], 201);
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
    // In your DutyProfController.php

    public function getRequestsForDuty($dutyId)
    {
        $professor = Auth::user();
    
        // Check if the authenticated user is a professor
        if (!$professor || $professor->role !== 'professor') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    
        // Retrieve the specific duty created by the professor
        $duty = Duty::where('id', $dutyId)
            ->where('prof_id', $professor->id)
            ->first();
    
        // Check if the duty exists and is created by this professor
        if (!$duty) {
            return response()->json(['message' => 'Duty not found or you do not have permission to view it'], 404);
        }
    
        // Get the requests for the duty with request_status as 'undecided'
        $requests = StudentDutyRecord::where('duty_id', $dutyId)
            ->where('request_status', 'undecided')
            ->get();
    
        // Count the number of requests
        $requestCount = $requests->count();
    
        // Get the names of students who have requested the duty
        $studentNames = $requests->map(function ($request) {
            $student = \App\Models\User::find($request->stud_id);
            return $student ? $student->name : 'Unknown';
        });
    
        // Return the simplified response
        return response()->json([
            'duty_id' => $dutyId,
            'request_count' => $requestCount,
            'student_names' => $studentNames
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
            ->get();

        return response()->json([
            'duty' => $duty,
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
            return response()->json(['message' => 'Duty not found or you do not have permission to accept students for it'], 404);
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

        // Get the specific duty created by the professor
        $duty = Duty::where('id', $dutyId)->where('prof_id', $professor->id)->first();

        if (!$duty) {
            return response()->json(['message' => 'Duty not found or you do not have permission to update its status'], 404);
        }

        // Validate the new status
        $data = $request->validate([
            'duty_status' => 'required|in:pending,active,completed',
        ]);

        // Update the duty status
        $duty->update(['duty_status' => $data['duty_status']]);

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

        // Lock the duty
        $duty->update(['is_locked' => true]);

        return response()->json(['message' => 'Duty locked successfully', 'duty' => $duty]);
    }
}
