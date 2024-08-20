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

    public function user(){
        return $this->belongsTo(User::class);
    }
}
