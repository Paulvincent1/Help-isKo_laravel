<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFeedback extends Model
{
    use HasFactory;

    protected $fillable = [

        'stud_id',
        'prof_id',
        'rating',
        'comment',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'stud_id');
    }

    public function professor()
    {
        return $this->belongsTo(User::class, 'prof_id');
    }

}
