<?php

namespace App\Http\Controllers\Duty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DutyNotificationsController extends Controller
{
    // Fetch and group notifications by date categories (Today, Yesterday, Specific Past Dates)
    public function index(Request $request)
    {
        $user = $request->user();

        // Fetch all notifications sorted by date
        $notifications = $user->notifications()->latest()->get();

        // Group notifications by date categories
        $groupedNotifications = [
            'today' => [],
            'yesterday' => [],
            'by_date' => []
        ];

        foreach ($notifications as $notification) {
            $createdDate = Carbon::parse($notification->created_at); // Parse created_at timestamp
            $today = Carbon::today();
            $yesterday = Carbon::yesterday();

            // Group by Today, Yesterday, or specific dates before that
            if ($createdDate->isToday()) {
                $groupedNotifications['today'][] = $this->formatNotification($notification);
            } elseif ($createdDate->isYesterday()) {
                $groupedNotifications['yesterday'][] = $this->formatNotification($notification);
            } else {
                // Group by specific date (e.g., "September 25, 2024")
                $dateString = $createdDate->format('F j, Y'); // Format date like "September 25, 2024"
                if (!isset($groupedNotifications['by_date'][$dateString])) {
                    $groupedNotifications['by_date'][$dateString] = [];
                }
                $groupedNotifications['by_date'][$dateString][] = $this->formatNotification($notification);
            }
        }

        // Return the grouped notifications
        return response()->json([
            'grouped_notifications' => $groupedNotifications
        ]);
    }

    // Format notification structure for consistency
    private function formatNotification($notification)
    {
        return [
            'title' => $notification->data['title'] ?? 'No title',
            'message' => $notification->data['message'] ?? 'No message',
            'icon' => $notification->data['icon'] ?? 'default_icon', 
            'date' => $notification->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
