<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudentFeedback;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentFeedbackController extends Controller
{
    public function index($student_id)
    {
        // Retrieve all feedback for the student, eager loading employee and employee profile
        $feedbacks = StudentFeedback::with(['employee.employeeProfile'])
            ->where('stud_id', $student_id)
            ->get();

        // Filter feedbacks to only include those with a non-null comment
        $filteredFeedbacks = $feedbacks->filter(function ($feedback) {
            return $feedback->comment !== null;
        });

        // Return feedback details as a simple indexed array
        return response()->json([
            'student_id' => $student_id,
            'feedbacks' => $filteredFeedbacks->values()->map(function ($feedback) {
                $employee = $feedback->employee;
                $profile = $employee ? $employee->employeeProfile : null;

                return [
                    'rating' => $feedback->rating,
                    'comment' => $feedback->comment,
                    'created_at' => $feedback->created_at,
                    'commenter_first_name' => $profile ? $profile->first_name : null,
                    'commenter_last_name' => $profile ? $profile->last_name : null,
                    'commenter_profile_img' => $profile ? $profile->profile_img : null,
                ];
            })
        ], 200);
    }

    public function showRating(User $id)
    {
        $user = $id;
        $feedbackReceives = $user->feedbackReceived; 
        $count = $user->feedbackReceived->count();

        $ratings = [];
        $totalRating = 0;

        // Collect only valid ratings (between 1 and 5)
        foreach($feedbackReceives as $feedbackReceived){
            if ($feedbackReceived->rating >= 1 && $feedbackReceived->rating <= 5) {
                $ratings[] = $feedbackReceived->rating;
            }
        }

        // Check if there are any valid ratings to avoid division by zero
        if (count($ratings) > 0) {
            foreach($ratings as $rating){
                $totalRating += $rating;
            }

            $ave = ($totalRating / count($ratings));
            $averageRating = round($ave, 1);

            $ratingCounts = array_count_values($ratings);

            $percentages = [];

            // Calculate percentage for each rating from 1 to 5
            for($i = 1; $i <= 5; $i++){
                $percent = isset($ratingCounts[$i]) ? (($ratingCounts[$i] / count($ratings)) * 100) : 0;
                $percentages[$i] = round($percent, 2);
            }
        } else {
            // If no valid ratings are found, set everything to 0
            $averageRating = 0;
            $percentages = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        }

        return response()->json([
            'average_rating' => $averageRating,
            'excellent'=> $percentages[5],
            'good' => $percentages[4],
            'average' =>$percentages[3],
            'below_average' => $percentages[2],
            'poor' => $percentages[1]
        ]);
    }

    public function storeRating(Request $request, $student_id)
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Validate the request data for rating
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5', // Make rating required
        ]);

        // Check if feedback already exists for this student and professor
        $feedback = StudentFeedback::where('stud_id', $student_id)
            ->where('prof_id', $user->id)
            ->first();

        if ($feedback) {
            // If feedback exists, update the rating with the new value
            $feedback->update([
                'rating' => $data['rating'],
            ]);
            $message = 'Rating updated successfully.';
        } else {
            // Create a new feedback entry if it does not exist
            $feedback = StudentFeedback::create([
                'stud_id' => $student_id,
                'prof_id' => $user->id,
                'rating' => $data['rating'],
            ]);
            $message = 'Rating created successfully.';
        }

        // Return the feedback as JSON with a 200 status, including the unique ID
        return response()->json([
            'message' => $message, 
            'feedback' => [
                'id' => $feedback->id, // Include the unique ID
                'rating' => $feedback->rating,
                'stud_id' => $feedback->stud_id,
                'prof_id' => $feedback->prof_id,
                // Include any other fields you want to return
            ]
        ], 200);
    }


    public function storeComment(Request $request, $student_id)
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Validate the request data for comment
        $data = $request->validate([
            'comment' => 'required|string|max:500',
        ]);

        // Create a new feedback entry with the comment
        $feedback = StudentFeedback::create([
            'stud_id' => $student_id,
            'prof_id' => $user->id,
            'comment' => $data['comment'],
        ]);

        // Return the feedback as JSON with a 200 status
        return response()->json($feedback, 200);
    }


    
};
