<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HkStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'remaining_hours',
        'duty_hours'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
