<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}
