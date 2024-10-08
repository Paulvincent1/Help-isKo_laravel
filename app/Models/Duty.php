<?php

namespace App\Models;

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
    public function getDutyStatusAttribute($value)
    {
        // If duty_status is 'cancelled' or 'completed' in the database, return it as is
        if (in_array($value, ['cancelled', 'completed'])) {
            return $value;
        }
    
        $currentTime = Carbon::now();
        $startTime = Carbon::parse($this->date . ' ' . $this->start_time);
        $endTime = Carbon::parse($this->date . ' ' . $this->end_time);
    
        // Initialize a variable to hold the new status
        $newStatus = $value; // Start with the existing value
    
        if ($this->is_locked) {
            $newStatus = 'active';
        }
    
        if ($this->is_locked && $currentTime->greaterThanOrEqualTo($endTime)) {
            $newStatus = 'completed';
        } elseif (!$this->is_locked && $currentTime->diffInSeconds($startTime) <= 60) {
            $newStatus = 'cancelled';
        } elseif ($currentTime->between($startTime, $endTime)) {
            $newStatus = 'ongoing';
        } else {
            $newStatus = 'pending';
        }
    
        // Update the database if the status has changed
        if ($newStatus !== $value) {
            $this->update(['duty_status' => $newStatus]); // Update the duty status in the database
        }
    
        return $newStatus; // Return the computed status
    }
    
}