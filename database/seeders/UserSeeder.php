<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'matt',
            'email' => 'matt@example.com',
            'password' => 'employee1',
            'role' => 'employee',
        ]);

        User::factory()->create([
            'name' => 'kris',
            'email' => 'kris@example.com',
            'password' => 'employee2',
            'role' => 'employee',
        ]);

        User::factory()->create([
            'name' => 'stud1',
            'email' => 'stud1@example.com',
            'password' => 'student1',
            'role' => 'student',
        ]);

        User::factory()->create([
            'name' => 'stud2',
            'email' => 'stud2@example.com',
            'password' => 'student2',
            'role' => 'student',
        ]);
    }
}
