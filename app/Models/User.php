<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function studentProfile(){
        return $this->hasOne(StudentProfile::class);
    }


    public function employeeProfile(){
        return $this->hasOne(EmployeeProfile::class);
    }



    
 // Assuming User can be a student
 public function feedbackReceived()
 {
     return $this->hasMany(StudentFeedback::class, 'stud_id');
 }

 // Assuming User can be a professor
 public function feedbackGiven()
 {
     return $this->hasMany(StudentFeedback::class, 'professor_id');
 }

    public function hkStatus(){
        return $this->hasOne(HkStatus::class);
    }




    
    public function studentDutyRecords()
    {
        return $this->hasMany(StudentDutyRecord::class, 'stud_id');
    }

    // Relationship to duties a professor creates
    public function duties()
    {
        return $this->hasMany(Duty::class, 'prof_id');
    }


    public function sentMessages(){
        return $this->hasMany(Message::class, 'sender_id');
    }
    
    public function receivedMessages(){
        return $this->hasMany(Message::class, 'receiver_id');
    }
}