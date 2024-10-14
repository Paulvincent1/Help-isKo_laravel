<?php

namespace App\Http\Controllers;

use App\Events\Message as EventsMessage;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function existingChats() {
        $user = Auth::user();
    
        // Fetch distinct chat participants
        $chats = Message::where('receiver_id', $user->id)->pluck('sender_id')
            ->merge(
                Message::where('sender_id', $user->id)->pluck('receiver_id')
            )->unique()
            ->filter(function ($id) use ($user) {
                return $id != $user->id;
            });
    
        $users = User::whereIn('id', $chats)->get();
        $messages = [];
    
        foreach ($users as $user) {
            // Fetch the latest sent and received messages
            $sent = $user->sentMessages()->latest()->first();
            $receive = $user->receivedMessages()->latest()->first();
    
            if ($sent && $receive) {
                $latestMessage = $sent->created_at > $receive->created_at ? $sent : $receive;
            } elseif ($sent) {
                $latestMessage = $sent;
            } elseif ($receive) {
                $latestMessage = $receive;
            }
    
            // Calculate unread messages where the current user is the receiver
            $unreadMessagesCount = Message::where('receiver_id', Auth::id())
                ->where('sender_id', $user->id)
                ->where('read_status', false) // Only unread messages
                ->count();
    
            // Get user profile based on role
            if ($user->role == 'employee') {
                $profile = $user->employeeProfile()->first() ?? '';
                $schoolId = $profile->employee_number ?? '';
            } else {
                $profile = $user->studentProfile()->first() ?? '';
                $schoolId = $profile->student_number ?? '';
            }
    
            // Add the message info to the array
            $messages[] = [
                'user_profile' => [
                    'user' => $user,
                    'profile_image' => $profile->profile_img ?? '',
                    'user_school_id' => $schoolId,
                ],
                'latest_message' => $latestMessage,
                'unreadMessagesCount' => $unreadMessagesCount, // Include unread count
            ];
        }
    
        // Sort messages by latest created_at timestamp
        usort($messages, function($a, $b) {
            return $b['latest_message']->created_at <=> $a['latest_message']->created_at;
        });
    
        return response()->json([
            'messages' => $messages,
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

    public function viewMessages(User $id) {
        $user = Auth::user();
        
        // Fetch the messages between the authenticated user and the other user (either as sender or receiver)
        $messages = Message::where(function ($query) use($id, $user) {
            $query->where('sender_id', $user->id)->where('receiver_id', $id->id)
                  ->orWhere(function ($query) use($id, $user) {
                      $query->where('sender_id', $id->id)->where('receiver_id', $user->id);
                  });
        })->get();
    
        // Mark all unread messages as read (only those where the authenticated user is the receiver)
        Message::where('sender_id', $id->id)
            ->where('receiver_id', $user->id)
            ->where('read_status', false)
            ->update(['read_status' => true]);
    
        return response()->json($messages);
    }
    
}
