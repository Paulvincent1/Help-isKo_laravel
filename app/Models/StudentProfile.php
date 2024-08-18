<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_number',
        'college',
        'course',
        'department',
        'semester',
        'birthday',
        'contact_number',
        'father_name',
        'mother_name',
        'current_address',
        'permanent_address',
        'profile_img'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
