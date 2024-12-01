<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RenewalForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_number',
        'attended_events',
        'shared_posts',
        'registration_fee_picture',
        'disbursement_method',
        'duty_hours',
        'approval_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
