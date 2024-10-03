<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;

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

        $res = Announcement::create($fields);

        return response()->json([
            'announcement' => $res,
            'message'=> 'successfully posted!'
        ],200);


    }

    public function show(Announcement $id){
        return response()->json([
            'announcement' => $id
        ]);
    }

    public function index(){

        $announcements = Announcement::all();
        return response()->json([
            'announcement' => $announcements
        ], 200);
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
        $res = $id->delete();
        return response()->json([
            'message' => $res
        ]);
    }

}
