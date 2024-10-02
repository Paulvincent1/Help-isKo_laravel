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
        $student = Auth::user(); 
    
        $duties = Duty::with('employee')
            ->where('duty_status', 'pending')
            ->where('is_locked', false)
            ->whereColumn('current_scholars', '<', 'max_scholars')
            ->whereDoesntHave('studentDutyRecords', function ($query) use ($student) {
                // Exclude duties that the student has already been accepted to
                $query->where('stud_id', $student->id)
                      ->where('request_status', 'accepted');
            })
            ->get();
    
        if ($duties->isEmpty()) {
            return response()->json(['message' => 'No available duties at the moment.'], 200);
        }
    
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
        return response()->json($response, 200);
    }


    // Request to join a specific duty
    public function requestDuty($dutyId)
    {
        $student = Auth::user();
        $duty = Duty::find($dutyId);

        if (!$duty || $duty->duty_status !== 'Pending' || $duty->is_locked) {
    
            $existingRequest = StudentDutyRecord::where('duty_id', $dutyId)
                ->where('stud_id', $student->id)
                ->first();
        
            if ($existingRequest) {
                return response()->json(['message' => 'You have already requested this duty'], 400);
            }
        
            $studentDutyRecord = StudentDutyRecord::create([
                'duty_id' => $dutyId,
                'stud_id' => $student->id,
                'emp_id' => $duty->emp_id,
                'request_status' => 'undecided',
            ]);
        
            return response()->json([
                'message' => 'Request submitted successfully',
                'request' => $studentDutyRecord,
                'duty' => $duty
            ], 201);
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
            'emp_id' => $duty->emp_id,
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

    // Added return duty information
    public function viewAcceptedDuties(Request $request)
{
    $student = Auth::user();

    // Fetch accepted duties for the student
    $query = StudentDutyRecord::with('duty.employee')  // Load the employee along with the duty
        ->where('stud_id', $student->id)
        ->where('request_status', 'accepted');

    // Optionally filter by status if provided (active, ongoing, completed, cancelled)
    if ($request->has('status')) {
        $status = $request->input('status');
        $query->whereHas('duty', function ($q) use ($status) {
            $q->where('duty_status', $status);
        });
    }

    $acceptedDuties = $query->get();

    // Check if any accepted duties were found
    if ($acceptedDuties->isEmpty()) {
        return response()->json(['message' => 'No accepted duties found'], 404);
    }

    // Format the response to include duty details and employee name
    $response = $acceptedDuties->map(function ($record) {
        return [
            'duty_id' => $record->duty->id,
            'building' => $record->duty->building,
            'date' => $record->duty->date,
            'start_time' => $record->duty->start_time,
            'end_time' => $record->duty->end_time,
            'status' => $record->duty->duty_status,
            'employee_name' => $record->duty->employee->name,  
        ];
    });

    return response()->json($response, 200);
}

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
