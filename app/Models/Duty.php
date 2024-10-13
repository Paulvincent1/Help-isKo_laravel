<?php

namespace App\Models;

use App\Notifications\Admin\StudentCompletedDutyNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Duty extends Model
{
    use HasFactory;

    protected $fillable = [
        'building',
        'emp_id',
        'date',
        'start_time',
        'end_time',
        'duration',
        'message',
        'max_scholars',
        'current_scholars',
        'is_locked',
        'duty_status',
        'is_completed',
    ];

    // Relationship with the intermediate model (StudentDutyRecord)
    public function studentDutyRecords()
    {
        return $this->hasMany(StudentDutyRecord::class, 'duty_id');
    }

    // Relationship to fetch the employee who created the duty
    public function employee()
    {
        return $this->belongsTo(User::class, 'emp_id');
    }

    /**
     * Get the duty status dynamically based on the current time.
     *
     * @param string $value
     * @return string
     */
    public function updateDutyStatus()
    {
        $currentTime = Carbon::now();
        $startTime = Carbon::parse($this->date . ' ' . $this->start_time);
        $endTime = Carbon::parse($this->date . ' ' . $this->end_time);

        // Determine the new status based on current time
        $newStatus = $this->duty_status; // Start with existing status
        $currentStatus = $this->duty_status;

        if ($this->is_locked) {
            if ($currentTime->greaterThanOrEqualTo($endTime)) {
                $newStatus = 'completed';
            }elseif ($currentTime->between($startTime, $endTime)) {
                $newStatus = 'ongoing';
            } else {
                $newStatus = 'active';
            }
        } elseif ($currentTime->diffInSeconds($startTime) <= 60) {
            $newStatus = 'cancelled';
        } else {
            $newStatus = 'pending';
        }

        // Update the database if the status has changed
        if ($newStatus !== $this->duty_status) {
            $this->update(['duty_status' => $newStatus]); // Update the duty status in the database
            \Log::info("Duty ID {$this->id} updated to status: {$newStatus}"); // Log the update
        }

        if($currentStatus != 'completed'){
            
            if($newStatus == 'completed'){
                $duties = $this->studentDutyRecords()->where('request_status','accepted')->with('student')->get();
                foreach($duties as $duty){  
                    $dutyHours = ($this->duration / 60);
                    $rounded = round($dutyHours , 2);
                    \Log::info("nag complete {$rounded}"); 
                        $remainingHours = $duty->student->hkStatus->remaining_hours;
                        if(($remainingHours - $rounded) >= 0) {
                            $duty->student->notify(new StudentCompletedDutyNotification($this));
                            $duty->student->hkStatus->update([
                                'remaining_hours' => ($remainingHours - $rounded)
                            ]);
                        }
                }
            }
        }
    }
}