<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentDutyRecordsTable extends Migration
{
    public function up(): void
    {
        Schema::create('student_duty_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stud_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('duty_id')->constrained('duties')->cascadeOnDelete();
            $table->foreignId('emp_id')->constrained('users')->cascadeOnDelete();
            $table->enum('request_status', ['undecided', 'accepted', 'rejected'])->default('undecided');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_duty_records');
    }
}
