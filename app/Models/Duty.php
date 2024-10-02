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

        if ($this->is_locked) {
            return 'active';
        }

        $currentTime = Carbon::now();
        $startTime = Carbon::parse($this->date . ' ' . $this->start_time);
        $endTime = Carbon::parse($this->date . ' ' . $this->end_time);

        if ($currentTime->greaterThanOrEqualTo($endTime)) {
            return 'completed';
        } elseif ($currentTime->between($startTime, $endTime)) {
            return 'ongoing';
        } elseif ($currentTime->greaterThanOrEqualTo($startTime)) {
            return 'active';
        } else {
            return 'pending';
        }
    }
}