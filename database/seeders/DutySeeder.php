<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Duty;

class DutySeeder extends Seeder
{
    /**
     * Seed the application's database with duties.
     */
    public function run(): void
    {
        Duty::create([
            'prof_id' => 1, 
            'description' => 'PTC 304',
            'duty_status' => 'pending',
            'max_scholars' => 2,
            'current_scholars' => 0,
            'is_locked' => false,
            'is_completed' => false,
            'building' => '3 Hays Building',
            'date' => now()->addDay()->format('Y-m-d'), 
            'start_time' => '09:00',
            'end_time' => '12:00',
            'duration' => 180, 
            'message' => 'Duty for Building A'
        ]);

        Duty::create([
            'prof_id' => 2,
            'description' => 'ITS 201',
            'duty_status' => 'pending',
            'max_scholars' => 1,
            'current_scholars' => 0,
            'is_locked' => false,
            'is_completed' => false,
            'building' => '4 Hays Building',
            'date' => now()->addDays(2)->format('Y-m-d'), 
            'start_time' => '13:00',
            'end_time' => '16:00',
            'duration' => 180,
            'message' => 'Duty for Building B'
        ]);
    }
}

