<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'employe_number',
        'birthday',
        'contact_number',
        'professor_number',
        'profile_img',

    ];





public function user(){
    $this->belongsTo(User::class);
}
};


