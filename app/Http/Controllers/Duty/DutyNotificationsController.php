<?php

namespace App\Http\Controllers\Duty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DutyNotificationsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Fetch only the notifications for the specific duty types (Active, Ongoing, Completed, Cancelled)
        $notifications = $user->notifications()
            ->whereIn('type', [
                'App\Notifications\DutyNotifications\ActiveDutyNotification',
                'App\Notifications\DutyNotifications\OngoingDutyNotification',
                'App\Notifications\DutyNotifications\CompletedDutyNotification',
                'App\Notifications\DutyNotifications\CancelledDutyNotification',
            ])
            ->latest()
            ->get();

        $groupedNotifications = [
            'today' => [],
            'yesterday' => [],
            'by_date' => []
        ];

        foreach ($notifications as $notification) {
            $createdDate = Carbon::parse($notification->created_at);
            $today = Carbon::today();
            $yesterday = Carbon::yesterday();

            if ($createdDate->isToday()) {
                $groupedNotifications['today'][] = $this->formatNotification($notification);
            } elseif ($createdDate->isYesterday()) {
                $groupedNotifications['yesterday'][] = $this->formatNotification($notification);
            } else {
                $dateString = $createdDate->format('F j, Y');
                if (!isset($groupedNotifications['by_date'][$dateString])) {
                    $groupedNotifications['by_date'][$dateString] = [];
                }
                $groupedNotifications['by_date'][$dateString][] = $this->formatNotification($notification);
            }
        }

        return response()->json(['grouped_notifications' => $groupedNotifications]);
    }

    private function formatNotification($notification)
    {
        $title = $notification->data['title'] ?? 'No title';
        $message = $notification->data['message'] ?? 'No message';
        $formattedDate = $notification->created_at->format('F j, Y h:i A');

        return [
            'title' => $title,
            'message' => $message,
            'date' => $formattedDate
        ];
    }
}
