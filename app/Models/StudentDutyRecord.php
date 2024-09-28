<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentDutyRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'stud_id',
        'duty_id',
        'emp_id',  
        'request_status',
    ];

    // Relationship back to User (student)
    public function student()
    {
        return $this->belongsTo(User::class, 'stud_id');
    }

    // Relationship back to Duty
    public function duty()
    {
        return $this->belongsTo(Duty::class, 'duty_id');
    }

    // Relationship back to Employee (if needed)
    public function employee()
    {
        return $this->belongsTo(User::class, 'emp_id');
    }
}
