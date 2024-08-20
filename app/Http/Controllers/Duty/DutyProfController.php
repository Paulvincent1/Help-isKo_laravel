<?php
namespace App\Http\Controllers\Duty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Duty;
use App\Models\StudentDutyRecord;

class DutyProfController extends Controller
{
    // Create a new duty
    public function create(Request $request)
    {
        $professor = Auth::user();

        
        $data = $request->validate([
            'building' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'message' => 'nullable|string',
            'max_scholars' => 'required|integer|min:1',
        ]);

        // Create the duty
        $duty = Duty::create([
            'building' => $data['building'],
            'date' => $data['date'],
            'time' => $data['time'],
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

    // Update a specific duty
    public function update($dutyId, Request $request)
    {
        $professor = Auth::user();
    
        // Get the specific duty created by the professor
        $duty = Duty::where('id', $dutyId)->where('prof_id', $professor->id)->first();
    
        if (!$duty) {
            return response()->json(['message' => 'Duty not found or you do not have permission to update it'], 404);
        }
    
        // Check if any students have requested this duty
        $hasRequests = StudentDutyRecord::where('duty_id', $dutyId)->exists();
    
        if ($hasRequests) {
            return response()->json(['message' => 'Cannot update duty as students have already requested it'], 400);
        }
    
        // Validate the incoming request data
        $data = $request->validate([
            'building' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'message' => 'nullable|string',
            'max_scholars' => 'required|integer|min:1',
        ]);
    
        // Calculate the new duration in minutes
        $startTime = \Carbon\Carbon::createFromFormat('H:i', $data['start_time']);
        $endTime = \Carbon\Carbon::createFromFormat('H:i', $data['end_time']);
        $duration = $startTime->diffInMinutes($endTime);
    
        // Update the duty
        $duty->update([
            'building' => $data['building'],
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'duration' => $duration,
            'message' => $data['message'],
            'max_scholars' => $data['max_scholars'],
        ]);
    
        return response()->json(['message' => 'Duty updated successfully', 'duty' => $duty]);
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

        // Ensure no students have requested the duty before deletion
        if (StudentDutyRecord::where('duty_id', $dutyId)->exists()) {
            return response()->json(['message' => 'Cannot delete duty as students have already requested it'], 400);
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
        $studentDutyRecord = StudentDutyRecord::where('duty_id', $dutyId)->where('stud_id', $studentId)->first();

        if (!$studentDutyRecord) {
            return response()->json(['message' => 'Student request not found'], 404);
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

    // Reject a student's request to join a duty
    public function rejectStudent($dutyId, $studentId)
    {
        $professor = Auth::user();

        // Get the specific duty created by the professor
        $duty = Duty::where('id', $dutyId)->where('prof_id', $professor->id)->first();

        if (!$duty) {
            return response()->json(['message' => 'Duty not found or you do not have permission to reject students for it'], 404);
        }

        // Find the student's request
        $studentDutyRecord = StudentDutyRecord::where('duty_id', $dutyId)->where('stud_id', $studentId)->first();

        if (!$studentDutyRecord) {
            return response()->json(['message' => 'Student request not found'], 404);
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
            'duty_status' => 'required|in:pending,active,completed,cancelled',
        ]);

        // Update the duty status
        $duty->update(['duty_status' => $data['duty_status']]);

        return response()->json(['message' => 'Duty status updated successfully', 'duty' => $duty]);
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
