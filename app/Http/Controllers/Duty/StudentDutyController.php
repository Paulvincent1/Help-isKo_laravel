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
        // Fetch duties that are pending, not locked, and have available slots, along with professor details
        $duties = Duty::with('employee')
            ->where('duty_status', 'pending')
            ->where('is_locked', false)
            ->whereColumn('current_scholars', '<', 'max_scholars')
            ->get();

        // Check if there are no duties found
        if ($duties->isEmpty()) {
            return response()->json(['message' => 'No available duties at the moment.'], 200);
        }

        // Prepare the response with professor names included
        $response = $duties->map(function ($duty) {
            return [
                'id' => $duty->id,
                'building' => $duty->building,
                'date' => $duty->date,
                'start_time' => $duty->start_time,
                'end_time' => $duty->end_time,
                'duration' => $duty->duration,
                'message' => $duty->message,
                'max_scholars' => $duty->max_scholars,
                'current_scholars' => $duty->current_scholars,
                'employee_name' => $duty->employee->name,  
            ];
        });

        // Return the list of available duties with professor names
        return response()->json($response, 200);
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
    
        return response()->json([
            'message' => 'Request submitted successfully',
            'request' => $studentDutyRecord,
            'duty' => $duty
        ], 201);
    }

    // View all duties the student has requested
    public function viewRequestedDuties()
    {
        $student = Auth::user();

        // Fetch only the duties that are still undecided
        $requestedDuties = StudentDutyRecord::where('stud_id', $student->id)
            ->where('request_status', 'undecided')
            ->with('duty') // Load the associated duty regardless of its status
            ->get();

        // Filter out any records where the duty is null
        $requestedDuties = $requestedDuties->filter(function ($record) {
            return $record->duty !== null;
        });

        // Check if there are no requested duties left after filtering
        if ($requestedDuties->isEmpty()) {
            return response()->json(['message' => 'You have no requested duties at the moment.'], 200);
        }

        // Return the list of requested duties
        return response()->json($requestedDuties);
    }


    // View details of a specific requested duty
    public function viewRequestedDutyDetails($dutyId)
    {
        $student = Auth::user();

        // Find the specific duty requested by the student
        $duty = Duty::find($dutyId);

        if (!$duty) {
            return response()->json(['message' => 'Duty not found'], 404);
        }

        // Find the student's request for the duty
        $studentDutyRecord = StudentDutyRecord::where('duty_id', $dutyId)
            ->where('stud_id', $student->id)
            ->where('request_status', 'undecided') // Only consider undecided requests
            ->first();

        if (!$studentDutyRecord) {
            return response()->json(['message' => 'You have not requested this duty or the request has already been processed.'], 400);
        }

        // Return the duty details along with the request status
        return response()->json([
            'duty' => $duty,
            'request_status' => $studentDutyRecord->request_status,
        ]);
    }


    // Cancel a request to join a duty
    public function cancelRequest($dutyId)
    {
        $student = Auth::user();

        // Fetch the student's duty request
        $studentDutyRecord = StudentDutyRecord::where('duty_id', $dutyId)
            ->where('stud_id', $student->id)
            ->first();

        // Check if the student's request exists and is still undecided
        if (!$studentDutyRecord || $studentDutyRecord->request_status !== 'undecided') {
            return response()->json(['message' => 'You cannot cancel this request as it has already been processed.'], 400);
        }

        // Proceed with cancellation
        $studentDutyRecord->delete();

        return response()->json(['message' => 'Request canceled successfully']);
    }

    public function viewAcceptedDuties()
    {
        $student = Auth::user();

        // Retrieve all duties where the student's request has been accepted
        $acceptedDuties = StudentDutyRecord::with('duty')
            ->where('stud_id', $student->id)
            ->where('request_status', 'accepted')
            ->get();

        // Check if any accepted duties were found
        if ($acceptedDuties->isEmpty()) {
            return response()->json(['message' => 'No accepted duties found'], 404);
        }

        return response()->json($acceptedDuties);
    }



    // View all completed duties by the student
    public function viewCompletedDuties()
    {
        $student = Auth::user();

        // Retrieve all completed duties for the authenticated student
        $completedDuties = StudentDutyRecord::where('stud_id', $student->id)
            ->where('request_status', 'accepted')
            ->whereHas('duty', function ($query) {
                $query->where('duty_status', 'completed');
            })
            ->with('duty')
            ->get();

        // Check if any completed duties were found
        if ($completedDuties->isEmpty()) {
            return response()->json(['message' => 'No completed duties found'], 404);
        }

        return response()->json($completedDuties);
    }



}
