<?php

namespace App\Http\Controllers;

use App\Events\Message as EventsMessage;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function existingChats(){

        $user = Auth::user();
        $chats = Message::where('receiver_id', $user->id)->pluck('sender_id')
        ->merge(
        Message::where('sender_id',$user->id)->pluck('receiver_id')
        )->unique()
        ->filter(function ($id) use($user){
            return $id != $user->id;
        });

        $users = User::whereIn('id',$chats)->get();
        $messages = [];
        foreach($users as $user){
            $sent = $user->sentMessages()->latest()->first();
            $receive = $user->receivedMessages()->latest()->first();

            if($sent && $receive){
                if($sent->created_at > $receive->created_at){
                    $latestMessage = $sent;
                }else{
                    $latestMessage = $receive;
                }
            }elseif($sent){
                $latestMessage = $sent;
            } elseif($receive) {
                $latestMessage = $receive;
            }
            if($user->role == 'employee'){
                $profile = $user->employeeProfile()->first() ?? '';
            }else{
                $profile = $user->studentProfile()->first() ?? '';
            }

            $messages[] = [
                'user_profile' => [
                    'user' => $user,
                    'profile_image' => $profile->profile_img ?? '',
                ],
               
                'latest_message' => $latestMessage
            ];
        }

        // <=> when the left side is greater it will return positive, so usort will pick the $b
        usort($messages,function($a, $b){
           return $b['latest_message']->created_at <=> $a['latest_message']->created_at;
        });
        

        return response()
        ->json([

            'messages' => $messages
        ]);

    }

    public function sendMessage(Request $request, User $id){
        $fields = $request->validate([
            'message' => 'required|string'
        ]);

        $message = Message::create([
            'message' => $fields['message'],
            'sender_id' => $request->user()->id,
            'receiver_id' => $id->id
        ]);

        broadcast(new EventsMessage($message));

        return response($message);

    }

    public function viewMessages(User $id){
        $user = Auth::user();
        $message = Message::where('sender_id', $user->id)->where('receiver_id', $id->id)
        ->orWhere(function ($query) use($id, $user){
            $query->where('sender_id', $id->id)->where('receiver_id', $user->id);
        })->get();

        return response($message);

    }
}
