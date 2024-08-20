<?php
namespace App\Http\Controllers\Duty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Duty;
use App\Models\StudentDutyRecord;

class StudentDutyController extends Controller
{
    // View all available duties for students to request
    public function viewAvailableDuties()
    {
        $duties = Duty::where('duty_status', 'pending')
                      ->where('is_locked', false)
                      ->get();

        return response()->json($duties);
    }

    // Request to join a specific duty
    public function requestDuty($dutyId)
    {
        $student = Auth::user();
        $duty = Duty::find($dutyId);

        if (!$duty || $duty->duty_status !== 'pending' || $duty->is_locked) {
            return response()->json(['message' => 'Duty is not available for requests'], 400);
        }

        $existingRequest = StudentDutyRecord::where('duty_id', $dutyId)
                                            ->where('stud_id', $student->id)
                                            ->first();

        if ($existingRequest) {
            return response()->json(['message' => 'You have already requested this duty'], 400);
        }

        $studentDutyRecord = StudentDutyRecord::create([
            'duty_id' => $dutyId,
            'stud_id' => $student->id,
            'request_status' => 'undecided',
        ]);

        return response()->json(['message' => 'Request submitted successfully', 'request' => $studentDutyRecord], 201);
    }

    // View all duties the student has requested
    public function viewRequestedDuties()
    {
        $student = Auth::user();
        $requestedDuties = StudentDutyRecord::where('stud_id', $student->id)->get();

        return response()->json($requestedDuties);
    }

    // View details of a specific requested duty
    public function viewRequestedDutyDetails($dutyId)
    {
        $student = Auth::user();
        $duty = Duty::where('id', $dutyId)->first();

        if (!$duty) {
            return response()->json(['message' => 'Duty not found'], 404);
        }

        $studentDutyRecord = StudentDutyRecord::where('duty_id', $dutyId)
                                              ->where('stud_id', $student->id)
                                              ->first();

        if (!$studentDutyRecord) {
            return response()->json(['message' => 'You have not requested this duty'], 400);
        }

        return response()->json($duty);
    }

    // Cancel a request to join a duty
    public function cancelRequest($dutyId)
    {
        $student = Auth::user();
        $studentDutyRecord = StudentDutyRecord::where('duty_id', $dutyId)
                                              ->where('stud_id', $student->id)
                                              ->where('request_status', 'undecided')
                                              ->first();

        if (!$studentDutyRecord) {
            return response()->json(['message' => 'You cannot cancel this request'], 400);
        }

        $studentDutyRecord->delete();

        return response()->json(['message' => 'Request canceled successfully']);
    }

    // View all completed duties by the student
    public function viewCompletedDuties()
{
    $student = Auth::user();

    // Retrieve all completed duties for the authenticated student
    $completedDuties = StudentDutyRecord::with(['duty' => function ($query) {
                                            $query->where('duty_status', 'completed');
                                        }])
                                        ->where('stud_id', $student->id)
                                        ->where('request_status', 'accepted')
                                        ->get();

    // Check if any completed duties were found
    if ($completedDuties->isEmpty()) {
        return response()->json(['message' => 'No completed duties found'], 404);
    }

    // Filter out any null duties (just in case)
    $filteredDuties = $completedDuties->filter(function ($record) {
        return $record->duty !== null;
    });

    return response()->json($filteredDuties);
}

    
}
