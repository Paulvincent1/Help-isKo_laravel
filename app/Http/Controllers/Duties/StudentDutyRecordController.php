<?php

namespace App\Http\Controllers\Duties;

use App\Http\Controllers\Controller;
use App\Models\StudentDutyRecord;
use App\Models\Duty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDutyRecordController extends Controller
{
    // View all available duties for the student
    public function index()
    {
        $student = Auth::user();
        
        $duties = Duty::whereHas('students', function($query) use ($student) {
            $query->where('stud_id', $student->id);
        })->get();

        return response()->json($duties, 200);
    }

    // Request to join a duty
    public function requestDuty($dutyId, Request $request)
    {
        $student = Auth::user();

        $existingRequest = StudentDutyRecord::where('stud_id', $student->id)
                                            ->where('duty_id', $dutyId)
                                            ->first();

        if ($existingRequest) {
            return response()->json(['message' => 'You have already requested to join this duty.'], 400);
        }

        $record = StudentDutyRecord::create([
            'stud_id' => $student->id,
            'duty_id' => $dutyId,
            'request_status' => 'undecided',
        ]);

        return response()->json($record, 201);
    }

    // View a specific duty request
    public function show($dutyId)
    {
        $student = Auth::user();

        $duty = Duty::whereHas('students', function($query) use ($student) {
            $query->where('stud_id', $student->id);
        })->with('students')->find($dutyId);

        if ($duty) {
            return response()->json($duty);
        } else {
            return response()->json(['message' => 'Duty not found or you are not enrolled in this duty'], 404);
        }
    }

    // Cancel a duty request
    public function cancelRequest($dutyId)
    {
        $student = Auth::user();

        $record = StudentDutyRecord::where('duty_id', $dutyId)
                                   ->where('stud_id', $student->id)
                                   ->where('request_status', 'undecided')
                                   ->first();

        if (!$record) {
            return response()->json(['message' => 'Cannot cancel request. It has already been processed or does not exist.'], 400);
        }

        $record->delete();

        return response()->json(['message' => 'Request canceled successfully'], 200);
    }
}
