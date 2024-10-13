<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AnnouncementController extends Controller
{
    public function store(Request $request){
        $fields = $request->validate([
            'heading' => 'required|string|max:255',
            'description' => 'required|string',
            'announcement_image' => 'image|mimes:jpg,bmp,png',
        ]);


        if($request->hasFile('announcement_image')){
            $file = $request->file('announcement_image');
            $filename = time() . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs('announcement_image',$filename,'public');

            $fields['announcement_image'] = 'storage/' . $path;

        }

        Announcement::create($fields);

        return redirect()->route('announcement');

    }

    public function create(){


        return view('announcement.announcement_add');
    }

    public function index(){
        $announcements = Announcement::all();
        return view('announcement.announcement', ['announcements' => $announcements]);
    }

    public function update(Request $request, Announcement $id){
        $fields = $request->validate([
            'heading' => 'required|string|max:255',
            'description' => 'required|string',
            'announcement_image' => 'image|mimes:jpg,bmp,png'
        ]);

        if($request->hasFile('announcement_image')){
            if(File::exists($id->announcement_image)){
                File::delete($id->announcement_image);
            }

            $file = $request->file('announcement_img');
            $filename = time() . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs('announcement_image',$filename, 'public');

            $fields['announcement_image'] = 'storage/' . $path;

        }

        $res = $id->update($fields);

        return response()->json([
            'message' => $res
        ]);

    }

    public function delete(Announcement $id){
        $id->delete();
        return redirect()->back();
    }
}
