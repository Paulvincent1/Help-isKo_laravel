<?php

namespace App\Http\Controllers\Admin;

use App\Models\RenewalForm;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\DutyNotifications\RenewalStatusNotification;

class AdminRenewalFormController extends Controller
{
    // Display all renewal requests on the renewal.renewal page
    public function index()
    {
        $renewalForms = RenewalForm::all();
        return view('renewal.renewal', compact('renewalForms'));
    }

    // Show details of a single renewal request (for the show.blade.php)
    public function show($id)
    {
        $renewalForm = RenewalForm::findOrFail($id);
        return view('renewal.show', compact('renewalForm'));
    }

    // Update the approval status of a renewal request
    public function updateRenewal(Request $request, $id)
    {
        $validatedData = $request->validate([
            'approval_status' => 'required|in:approved,rejected',
        ]);

        $renewalForm = RenewalForm::findOrFail($id);
        $renewalForm->update(['approval_status' => $validatedData['approval_status']]);

        $user = $renewalForm->user; // Assuming RenewalForm has a relation to User

        // Send notification to the user
        $user->notify(new RenewalStatusNotification($renewalForm, $validatedData['approval_status']));

        return redirect()->route('renewal')
                         ->with('success', 'Renewal request updated successfully.');
    }

    // Delete a renewal request
    public function deleteRenewal($id)
    {
        $renewalForm = RenewalForm::findOrFail($id);
        $renewalForm->delete();

        return redirect()->route('renewal')->with('message', 'Form deleted successfully!');
    }
}
