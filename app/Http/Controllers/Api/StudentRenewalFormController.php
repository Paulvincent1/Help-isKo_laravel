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

        // Retrieve the user's HK status and calculate the percentage
        $hkStatus = $user->hkStatus;
        if ($hkStatus) {
            $dutyHours = (float) $hkStatus->duty_hours;
            $remainingHours = (float) $hkStatus->remaining_hours;

            $completedHours = $dutyHours - $remainingHours;
            $percentage = ($completedHours / $dutyHours) * 100;

            // If the HK status percentage is not 100%, reject the form submission
            if ($percentage != 100) {
                return response()->json(['message' => 'Form submission is only allowed when the HK status percentage is 100%.'], 400);
            }
        } else {
            return response()->json(['message' => 'No HK status found.'], 404);
        }

        // Check if the student already has a pending renewal form
        $existingForm = RenewalForm::where('user_id', $user->id)
                                    ->where('approval_status', 'pending')
                                    ->first();

        if ($existingForm) {
            return response()->json(['message' => 'You already have a pending renewal form. Please wait for approval before submitting another one.'], 409);
        }

        // Validate incoming request
        $validatedData = $request->validate([
            'student_number' => 'required|string',
            'attended_events' => 'required|integer|min:0',
            'shared_posts' => 'required|integer|min:0',
            'registration_fee_picture' => 'nullable|string',
            'disbursement_method' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
            'duty_hours' => 'required|integer',
        ]);

        // Assuming registration_fee_picture is a string URL
        $registrationFeePath = $validatedData['registration_fee_picture'] ?? '';

        // Handle disbursement method file upload
        $disbursementMethodPath = '';
        if ($request->hasFile('disbursement_method')) {
            $file = $request->file('disbursement_method');
            $fileName = time() . '_disbursement.' . $file->getClientOriginalExtension();
            $disbursementMethodPath = $file->storeAs('uploads/disbursement_methods', $fileName, 'public');
        }

        // Create the renewal form record
        $renewalForm = RenewalForm::create([
            'user_id' => $user->id,
            'student_number' => $validatedData['student_number'],
            'attended_events' => $validatedData['attended_events'],
            'shared_posts' => $validatedData['shared_posts'],
            'registration_fee_picture' => $registrationFeePath,
            'disbursement_method' => $disbursementMethodPath,
            'duty_hours' => $validatedData['duty_hours'],
            'approval_status' => 'pending',
        ]);

        // Return the response
        return response()->json([
            'message' => 'Renewal form submitted successfully!',
            'renewal_form' => $renewalForm,
        ], 201);
    }


    public function show($id)
    {
        // Retrieve the renewal form
        $renewalForm = RenewalForm::findOrFail($id);

        return response()->json([
            'renewal_form' => $renewalForm,
        ], 200);
    }
}
