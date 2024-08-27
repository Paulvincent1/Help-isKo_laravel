<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Duty extends Model
{
    use HasFactory;

    protected $fillable = [
        'building',
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
        'prof_id',
    ];

    // Relationship with the intermediate model (StudentDutyRecord)
    public function studentDutyRecords()
    {
        return $this->hasMany(StudentDutyRecord::class, 'duty_id');
    }

    public function professor()
{
    return $this->belongsTo(User::class, 'prof_id');
}

}
