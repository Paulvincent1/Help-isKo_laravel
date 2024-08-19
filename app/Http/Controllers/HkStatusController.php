<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HkStatusController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $hkStatus = $user->hkStatus;
        $percentage = 0;

        if ($hkStatus) {
            $dutyHours = (float) $hkStatus->duty_hours;
            $remainingHours = (float) $hkStatus->remaining_hours;

            $completedHours = $dutyHours - $remainingHours;
            $percentage = ($completedHours / $dutyHours) * 100;

            return response()->json([
                'name' => $user->name,
                'hk-status' => $hkStatus,
                'percentage' => round($percentage, 2) . '%'
            ]);
        }

        return response()->json([
            'message' => 'No HK Status found'
        ], 404);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->hkStatus == null) {

            $fields = $request->validate([
                'remaining_hours' => 'required|string|max:255',
                'duty_hours' => 'required|string|max:255',
            ]);

            $res = $user->hkStatus()->create($fields);
            return response($res, 200);
        }
        return response()->json([
            'message' => 'Your Status Filled!'
        ], 403);
    }

}
