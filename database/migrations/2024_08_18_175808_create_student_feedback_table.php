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
        Schema::create('student_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prof_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('stud_id')->constrained('users')->cascadeOnDelete();
            $table->integer('rating')->nullable(); // E.g., 1 to 5
            $table->text('comment')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_feedback');
    }
};
