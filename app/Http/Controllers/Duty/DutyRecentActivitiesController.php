<?php

namespace App\Http\Controllers\Duty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Duty;
use App\Notifications\DutyRecentActivities\Student\StudentDutyRequestedNotification;  // Added import for student duty notification

class DutyRecentActivitiesController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Fetch only the 10 most recent notifications, filtering by types for recent activities
        $recentActivities = $user->notifications()
                                 ->whereIn('type', [
                                     'App\Notifications\DutyRecentActivities\DutyPostedNotification',
                                     'App\Notifications\DutyRecentActivities\DutyRemovedNotification',
                                     'App\Notifications\DutyRecentActivities\DutyEditedNotification',
                                     'App\Notifications\DutyRecentActivities\Student\StudentDutyRequestedNotification'  
                                 ])
                                 ->latest()
                                 ->limit(10)
                                 ->get();

        $formattedActivities = [];
        foreach ($recentActivities as $activity) {
            $formattedActivities[] = $this->formatActivity($activity);
        }

        return response()->json([
            'recent_activities' => $formattedActivities,
        ]);
    }

    private function formatActivity($activity)
    {
        // Set correct titles based on the type of notification
        if ($activity->type === 'App\Notifications\DutyRecentActivities\DutyPostedNotification') {
            $title = 'Created';
            $description = 'You posted a duty!';
        } elseif ($activity->type === 'App\Notifications\DutyRecentActivities\DutyEditedNotification') {
            $title = 'Updated';
            $description = 'You edited a duty!';
        } elseif ($activity->type === 'App\Notifications\DutyRecentActivities\DutyRemovedNotification') {
            $title = 'Deleted';
            $description = 'You removed a duty!';
        } elseif ($activity->type === 'App\Notifications\DutyRecentActivities\Student\StudentDutyRequestedNotification') {
            $title = 'Requested';
            $description = 'You requested a duty!';
        } else {
            $title = 'No title';
            $description = 'No description';
        }

        // For Create, Update, and Request, return the duty info
        $dutyInfo = null;
        if (in_array($title, ['Created', 'Updated', 'Requested'])) {
            $dutyInfo = $this->getDutyInfo($activity->data['duty_id']);
        }

        return [
            'title' => $title,
            'description' => $description,
            'message' => $activity->data['message'] ?? 'No message available',
            'date' => $this->getFormattedDate($activity->created_at),  // Display formatted date (Today, Yesterday, or full date)
            'duty_info' => $dutyInfo  // Include duty info for create, update, and requested activities
        ];
    }

    private function getDutyInfo($dutyId)
    {
        // Find the duty by ID
        $duty = Duty::find($dutyId);

        if (!$duty) {
            return null;  // If no duty is found, return null
        }

        // Return the duty details as needed
        return [
            'building' => $duty->building,
            'date' => $duty->date,
            'start_time' => $duty->start_time,
            'end_time' => $duty->end_time,
            'message' => $duty->message,
            'max_scholars' => $duty->max_scholars,
            'current_scholars' => $duty->current_scholars,
            'status' => $duty->duty_status
        ];
    }

    private function getFormattedDate($date)
    {
        // Return formatted date as Today, Yesterday, or the full date
        if ($date->isToday()) {
            return 'Today, ' . $date->format('g:i A');  
        } elseif ($date->isYesterday()) {
            return 'Yesterday, ' . $date->format('g:i A');  
        } elseif ($date->greaterThanOrEqualTo(Carbon::today()->subDays(2))) {
            return '2 days ago, ' . $date->format('g:i A');  
        } else {
            return $date->format('F j, Y, g:i A');
        }
    }
}
