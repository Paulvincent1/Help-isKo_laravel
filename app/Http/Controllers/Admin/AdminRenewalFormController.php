<?php

namespace App\Http\Controllers\Admin;

use App\Models\RenewalForm;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminRenewalFormController extends Controller
{
    public function index()
    {
        $renewalForms = RenewalForm::all();
        return view('renewal.renewal', compact('renewalForms'));
    }

    public function show($id)
    {
        $renewalForm = RenewalForm::findOrFail($id);
        return view('admin.renewal_forms.show', compact('renewalForm'));
    }

    public function updateStatus(Request $request, $id)
    {
        $validatedData = $request->validate([
            'approval_status' => 'required|in:approved,rejected',
        ]);

        $renewalForm = RenewalForm::findOrFail($id);
        $renewalForm->update(['approval_status' => $validatedData['approval_status']]);

        return redirect()->route('admin.renewal_forms.index')->with('message', 'Form status updated successfully!');
    }

    public function destroy($id)
    {
        $renewalForm = RenewalForm::findOrFail($id);
        $renewalForm->delete();

        return redirect()->route('admin.renewal_forms.index')->with('message', 'Form deleted successfully!');
    }
}
