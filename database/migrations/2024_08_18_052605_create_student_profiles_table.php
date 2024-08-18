<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('student_number');
            $table->string('college');
            $table->string('course');
            $table->string('department');
            $table->string('semester');
            $table->string('birthday');
            $table->string('contact_number');
            $table->string('father_name');
            $table->string('mother_name');
            $table->string('current_address');
            $table->string('permanent_address');
            $table->string('profile_img')->nullable(true);
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
