<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AnnouncementController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming request data
        $fields = $request->validate([
            'heading' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'announcement_image' => 'nullable|image|mimes:jpg,bmp,png', // Make image field nullable for web forms
        ]);

        // Handle file upload if present
        if ($request->hasFile('announcement_image')) {
            $file = $request->file('announcement_image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('announcement_image', $filename, 'public');
            $fields['announcement_image'] = 'storage/' . $path;
        }

        // Create a new announcement
        Announcement::create($fields);

        // Redirect to the index page with a success message
        return redirect()->route('announcement')->with('success', 'Announcement successfully posted!');
    }


    public function index()
    {
    // Fetch all announcements from the database
    $announcements = Announcement::all();

    // Return the view with the announcements data
    return view('announcement.announcement', compact('announcements'));
    }


    public function update(Request $request, Announcement $announcement)
    {
    $fields = $request->validate([
        'heading' => 'required|string|max:255',
        'description' => 'required|string',
        'announcement_image' => 'nullable|image|mimes:jpg,bmp,png'
    ]);

    if ($request->hasFile('announcement_image')) {
        // Delete the old image if it exists
        if (File::exists(public_path($announcement->announcement_image))) {
            File::delete(public_path($announcement->announcement_image));
        }

        // Store the new image
        $file = $request->file('announcement_image');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('announcement_image', $filename, 'public');
        $fields['announcement_image'] = 'storage/' . $path;
    }

    $announcement->update($fields);

    return redirect()->route('announcement')->with('success', 'Announcement successfully updated!');
    }

    public function delete(Announcement $id)
    {
    try {
        // Attempt to delete the announcement
        $id->delete();

        // Redirect back with a success message
        return redirect()->route('announcement')
                         ->with('success', 'Announcement deleted successfully.');
    } catch (\Exception $e) {
        // Handle the exception and redirect back with an error message
        return redirect()->route('announcement')
                         ->with('error', 'Failed to delete the announcement.');
    }
    }

}
