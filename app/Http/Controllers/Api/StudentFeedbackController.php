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
        // Retrieve all feedback for the student
        $feedbacks = StudentFeedback::with('employee')->where('stud_id', $student_id)->get();

        // Return feedback details
        return response()->json([
            'student_id' => $student_id,
            'feedbacks' => $feedbacks->map(function ($feedback) {
                return [
                    'rating' => $feedback->rating,
                    'comment' => $feedback->comment,
                    'created_at' => $feedback->created_at,
                    'commenter_first_name' => $feedback->employee ? $feedback->employee->first_name : null,
                    'commenter_last_name' => $feedback->employee ? $feedback->employee->last_name : null,
                    'commenter_profile_img' => $feedback->employee ? $feedback->employee->profile_img : null,
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

        foreach($feedbackReceives as $feedbackReceived){
            $ratings[] = $feedbackReceived->rating;
        }

        foreach($ratings as $rating){
            $totalRating+=$rating;
        }

        $ave = ($totalRating / $count);
        $averageRating = round($ave, 1);

        $ratingCounts = array_count_values($ratings);

        $percentages = [];

        for($i = 1; $i <= 5; $i++){
            $percent = isset($ratingCounts[$i]) ? (($ratingCounts[$i] / $count) * 100) : 0;
            $percentages[$i] = round($percent, 2);
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
   
   
    public function show($id)
    {
        // Retrieve the feedback by ID
        $feedback = StudentFeedback::find($id);
    
        // Check if feedback exists
        if ($feedback) {

            //average rating
            $averageRating = StudentFeedback::where('stud_id', $feedback->stud_id)
            ->whereNotNull('rating') // Ensure only entries with ratings are considered
            ->average('rating');
    
            //get the first 2 of point digit 
            $formattedAverageRating = number_format($averageRating, 2);
        
            return response()->json([
                'id' => $feedback->id,
                'stud_id' => $feedback->stud_id,
                'prof_id' => $feedback->prof_id,
                'rating' => $feedback->rating,
                'average_rating' => $averageRating,
                'comment' => $feedback->comment,
                'created_at' => $feedback->created_at,
                'updated_at' => $feedback->updated_at,
               
            ], 200);
        } else {
            // Return not found response
            return response()->json(['message' => 'Feedback not found'], 404);
        }
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $student_id)
    {
        // Get the currently authenticated user
        $user = Auth::user();
    
        // Validate the request data
        $data = $request->validate([
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'required|string|max:500'
        ]);

    
        // Create a new feedback entry
        $feedback = StudentFeedback::create([
            'stud_id' => $student_id,
            'prof_id' => $user->id,
            'rating' => $data['rating'] ?? null, // Fixed the typo here
            'comment' => $data['comment'],
        ]);


    
        // Return the feedback as JSON with a 200 status
        return response()->json($feedback, 200);
    }
    
};
