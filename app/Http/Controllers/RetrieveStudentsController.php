<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\StudentDutyRecord;
use App\Models\StudentFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RetrieveStudentsController extends Controller
{
    public function index()
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Get the authenticated user
        $user = Auth::user();

        // Check if the user is an employee
        if ($user->role !== 'employee') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Retrieve all students
        $students = User::where('role', 'student')->with('studentProfile')->get();

        $response = [];

        foreach ($students as $student) {
            // Get the count of active duties (those that are accepted and is_locked)
            $activeDutiesCount = StudentDutyRecord::where('stud_id', $student->id)
                ->whereHas('duty', function ($query) {
                    $query->where('is_locked', true)
                          ->where('duty_status', 'active');
                })
                ->count();

            $completedDutiesCount = StudentDutyRecord::where('stud_id', $student->id)
                ->whereHas('duty', function ($query) {
                    $query->where('is_locked', true)
                          ->where('duty_status', 'completed');
                })
                ->count();

            // Get the average rating for the student from feedback
            $averageRating = StudentFeedback::where('stud_id', $student->id)
                ->whereNotNull('rating')
                ->average('rating');

            $formattedAverageRating = $averageRating ? number_format($averageRating, 2) : 'No Rating';

            $hkStatus = $student->hkStatus;
            $percentage = 0;

            if ($hkStatus) {
                $dutyHours = (float) $hkStatus->duty_hours;
                $remainingHours = (float) $hkStatus->remaining_hours;

                if ($dutyHours > 0) {
                    $completedHours = $dutyHours - $remainingHours;
                    $percentage = ($completedHours / $dutyHours) * 100;
                }
            }

            // Prepare student data
            $response[] = [
                'student_id' => $student->id,
                'email' => $student->email,
                'name' => $student->name,
                'profile_image' => $student->studentProfile->profile_img ?? 'Unknown',
                'student_number' => $student->studentProfile->student_number ?? 'Unknown',
                'course' => $student->studentProfile->course ?? 'Unknown',
                'department' => $student->studentProfile->department ?? 'Unknown',
                'learning_modality' => $student->studentProfile->learning_modality ?? 'Unknown',
                'semester' => $student->studentProfile->semester ?? 'Unknown',
                'birthday' => $student->studentProfile->birthday ?? 'Unknown',
                'contact_number' => $student->studentProfile->contact_number ?? 'Unknown',
                'active_duty_count' => $activeDutiesCount,
                'completed_duty_count' => $completedDutiesCount,
                'average_rating' => $formattedAverageRating,
                'percentage' => round($percentage, 2)
            ];
        }

        return response()->json($response);
    }
}
