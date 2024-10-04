<?php

namespace App\Http\Controllers\Api;

use App\Models\RenewalForm;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StudentRenewalFormController extends Controller
{
    // Store a new renewal form
    public function store(Request $request)
    {
        // Ensure the authenticated user is a student
        $user = Auth::user();
        if ($user->role !== 'student') {
            return response()->json(['message' => 'Unauthorized. Only students can submit renewal forms.'], 403);
        }
    
        $validatedData = $request->validate([
            'student_number' => 'required|string', 
            'attended_events' => 'required|integer|min:0',
            'shared_posts' => 'required|integer|min:0',
            'registration_fee_picture' => 'required|url',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'disbursement_method' => 'required|string',
            'duty_hours' => 'required|integer',
        ]);

   
        $filePath = $validatedData['registration_fee_picture']; 
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads/registration_fees', $fileName, 'public');
        }

        $renewalForm = RenewalForm::create([
            'user_id' => $user->id,  
            'student_number' => $validatedData['student_number'],  
            'attended_events' => $validatedData['attended_events'],
            'shared_posts' => $validatedData['shared_posts'],
            'registration_fee_picture' => $filePath,
            'disbursement_method' => $validatedData['disbursement_method'],
            'duty_hours' => $validatedData['duty_hours'],
            'approval_status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Renewal form submitted successfully!',
            'renewal_form' => $renewalForm,
        ], 201);
    }

    public function show($id)
    {
        // Get the renewal form
        $renewalForm = RenewalForm::findOrFail($id);

        return response()->json([
            'renewal_form' => $renewalForm,
        ], 200);
    }
}
