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
            'email' => 'profmatt@example.com',
            'password' => 'professor1',
            'role' => 'professor',
        ]);

        User::factory()->create([
            'name' => 'prof2',
            'email' => 'prof2@example.com',
            'password' => 'professor2',
            'role' => 'professor',
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
