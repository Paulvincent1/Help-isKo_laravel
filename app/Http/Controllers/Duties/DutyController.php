<?php
namespace App\Http\Controllers\Duties;

use App\Http\Controllers\Controller;
use App\Models\Duty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DutyController extends Controller
{
    // Method to display all duties
    public function show($dutyId)
    {
        $duty = Duty::with('students')->find($dutyId);

        if ($duty) {
            return response()->json($duty);
        } else {
            return response()->json(['message' => 'Duty not found'], 404);
        }
    }
    
    // Method to create a new duty (Professors only)
    public function create(Request $request)
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Ensure the user is a professor
        if ($user->role !== 'professor') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validate the request data
        $data = $request->validate([
            'building' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'message' => 'nullable|string',
            'max_scholars' => 'required|integer|min:1',
        ]);

        // Create a new duty entry
        $duty = Duty::create([
            'building' => $data['building'],
            'date' => $data['date'],
            'time' => $data['time'],
            'message' => $data['message'],
            'max_scholars' => $data['max_scholars'],
            'prof_id' => $user->id,
            'current_scholars' => 0,
            'is_locked' => false,
            'duty_status' => 'pending',
        ]);

        // Return the duty as JSON with a 201 status
        return response()->json($duty, 201);
    }
}
