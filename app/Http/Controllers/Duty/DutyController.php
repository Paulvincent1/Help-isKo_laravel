<?php
namespace App\Http\Controllers\Duty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Duty;

class DutyController extends Controller
{
    // View all available duties (Pending and not locked)
    // functionaity for both prof and students
    public function index()
    {
        $duties = Duty::where('duty_status', 'pending')
                      ->where('is_locked', false)
                      ->get();

        return response()->json($duties);
    }

    // View details of a specific duty
    public function show($dutyId)
    {
        $duty = Duty::find($dutyId);

        if (!$duty) {
            return response()->json(['message' => 'Duty not found'], 404);
        }

        return response()->json($duty);
    }

    // Check the status of a specific duty
    public function checkStatus($dutyId)
    {
        $duty = Duty::find($dutyId);

        if (!$duty) {
            return response()->json(['message' => 'Duty not found'], 404);
        }

        return response()->json([
            'max_scholars' => $duty->max_scholars,
            'current_scholars' => $duty->current_scholars,
            'is_locked' => $duty->is_locked
        ]);
    }
}
