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
            $table->string('first_name');
            $table->string('last_name');
            $table->string('student_number');
            $table->string('college');
            $table->string('course');
            $table->string('department');
            $table->string('semester');
            $table->string('learning_modality');
            $table->string('birthday');
            $table->string('contact_number');

            // famlly
            $table->string('father_name');
            $table->string('father_contact_number');
            $table->string('mother_name');
            $table->string('mother_contact_number');

            //current address
            $table->string('current_address');
            $table->string('current_province');
            $table->string('current_country');
            $table->string('current_city');

            //permanent address
            $table->string('permanent_address');
            $table->string('permanent_province');
            $table->string('permanent_country');
            $table->string('permanent_city');

            // emergency person contact details
            $table->string('emergency_person_name');
            $table->string('emergency_address');
            $table->string('relation');
            $table->string('emergency_contact_number');


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
