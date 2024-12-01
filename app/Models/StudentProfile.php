<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StudentDutyRecord;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'student_number',
        'college',
        'course',
        'department',
        'learning_modality',
        'semester',
        'birthday',
        'contact_number',

        // family
        'father_name',
        'father_contact_number',
        'mother_name',
        'mother_contact_number',

        // current address
        'current_address',
        'current_province',
        'current_country',
        'current_city',

        // permanent address
        'permanent_address',
        'permanent_province',
        'permanent_country',
        'permanent_city',

        // emergency person contact details
        'emergency_person_name',
        'emergency_address',
        'relation',
        'emergency_contact_number',

        'profile_img'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor for active duties count
    public function getActiveDutiesCountAttribute()
    {
        return StudentDutyRecord::where('stud_id', $this->user->id)
            ->whereHas('duty', function ($query) {
                $query->where('duty_status', 'active');
            })
            ->count();
    }

    // Accessor for ongoing duties count
    public function getOngoingDutiesCountAttribute()
    {
        return StudentDutyRecord::where('stud_id', $this->user->id)
            ->whereHas('duty', function ($query) {
                $query->where('duty_status', 'ongoing');
            })
            ->count();
    }

    // Accessor for completed duties count
    public function getCompletedDutiesCountAttribute()
    {
        return StudentDutyRecord::where('stud_id', $this->user->id)
            ->whereHas('duty', function ($query) {
                $query->where('duty_status', 'completed');
            })
            ->count();
    }

    // Accessor for total duties count
    public function getTotalDutiesCountAttribute()
    {
        return StudentDutyRecord::where('stud_id', $this->user->id)
            ->where('request_status', 'accepted')
            ->count();
    }
}
