<?php

namespace App\Http\Controllers\Duty;

use App\Http\Controllers\Controller;
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

        return response()->json(['message' => 'Duty created successfully', 'duty' => $duty], 201);
    }

    // View all duties created by the employee
    public function index()
    {
        $employee = Auth::user();

        // Get all duties created by this employee
        $duties = Duty::where('emp_id', $employee->id)->get();

        return response()->json($duties);
    }

    // View a specific duty created by the employee
    public function show($dutyId)
    {
        $employee = Auth::user();

        // Get the specific duty created by the employee
        $duty = Duty::where('id', $dutyId)->where('emp_id', $employee->id)->first();

        if (!$duty) {
            return response()->json(['message' => 'Duty not found or you do not have permission to view it'], 404);
        }

        return response()->json($duty);
    }

    // Retrieve all pending requests for duties created by the employee
    public function getRequestsForAllDuties($empId)
    {
        // Fetch the employee using the provided ID and check role
        $employee = \App\Models\User::where('id', $empId)->where('role', 'employee')->first();
        if (!$employee) {
            return response()->json(['message' => 'Employee not found or unauthorized'], 404);
        }

        // Retrieve all duties created by the employee that are pending and not locked
        $duties = Duty::where('emp_id', $empId)
                      ->where('duty_status', 'pending')
                      ->where('is_locked', false)
                      ->get();
        if ($duties->isEmpty()) {
            return response()->json(['message' => 'No pending duties found for this employee'], 404);
        }

        // Process duties to get details and associated undecided requests
        $dutyDetails = $duties->map(function ($duty) {
            $requests = StudentDutyRecord::where('duty_id', $duty->id)
                                         ->where('request_status', 'undecided')
                                         ->get();

            // Compile student data for each request
            $studentData = $requests->map(function ($request) {
                $student = \App\Models\User::find($request->stud_id);
                return [
                    'student_id' => $request->stud_id,
                    'name' => $student ? $student->name : 'Unknown'
                ];
            });

            // Return comprehensive duty details
            return [
                'duty_id' => $duty->id,
                'current_scholars' => $duty->current_scholars,
                'max_scholars' => $duty->max_scholars,
                'request_count' => $requests->count(),
                'student_data' => $studentData
            ];
        });

        return response()->json($dutyDetails);
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

        // Check if any student requests have been accepted for this duty
        $hasAcceptedRequests = StudentDutyRecord::where('duty_id', $dutyId)
            ->where('request_status', 'accepted')
            ->exists();

        if ($hasAcceptedRequests) {
            return response()->json(['message' => 'Cannot update duty details as there are accepted student requests'], 400);
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
 
     // Accept a student's request
     public function acceptStudent($recordId)
     {
         $employee = Auth::user();
 
         // Find the student's duty request by its ID
         $studentDutyRecord = StudentDutyRecord::find($recordId);
 
         if (!$studentDutyRecord) {
             return response()->json(['message' => 'Student request not found'], 404);
         }
 
         // Get the duty and ensure the employee owns it
         $duty = Duty::where('id', $studentDutyRecord->duty_id)
                     ->where('emp_id', $employee->id)
                     ->first();
 
         if (!$duty) {
             return response()->json(['message' => 'Duty not found or you do not have permission to handle student requests for it'], 404);
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
 
     // Reject a student's request
     public function rejectStudent($recordId)
{
    $employee = Auth::user();

    // Find the student's duty request by its ID
    $studentDutyRecord = StudentDutyRecord::find($recordId);

    if (!$studentDutyRecord) {
        return response()->json(['message' => 'Student request not found'], 404);
    }

    // Get the duty and ensure the employee owns it
    $duty = Duty::where('id', $studentDutyRecord->duty_id)
                ->where('emp_id', $employee->id)
                ->first();

    if (!$duty) {
        return response()->json(['message' => 'Duty not found or you do not have permission to handle student requests for it'], 404);
    }

    // Prevent changing the decision once made
    if ($studentDutyRecord->request_status !== 'undecided') {
        return response()->json(['message' => 'This request has already been ' . $studentDutyRecord->request_status . ' and cannot be changed.'], 400);
    }

    // Optionally, soft delete the student's request
    $studentDutyRecord->delete();  // Soft delete if SoftDeletes trait is used

    // Optionally, log the rejection for tracking purposes
    \Log::info('Student request rejected', [
        'employee_id' => $employee->id,
        'student_id' => $studentDutyRecord->stud_id,
        'duty_id' => $studentDutyRecord->duty_id,
        'rejected_at' => now(),
    ]);

    return response()->json(['message' => 'Student request rejected and deleted successfully']);
}

 
     // Cancel a duty
     public function cancelDuty($dutyId)
     {
         $employee = Auth::user();
 
         // Get the specific duty created by the employee
         $duty = Duty::where('id', $dutyId)->where('emp_id', $employee->id)->first();
 
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
         $employee = Auth::user();
 
         // Get the specific duty created by the employee
         $duty = Duty::where('id', $dutyId)->where('emp_id', $employee->id)->first();
 
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