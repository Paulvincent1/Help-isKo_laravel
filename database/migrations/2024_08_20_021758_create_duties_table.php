<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDutiesTable extends Migration
{
    public function up(): void
    {
        Schema::create('duties', function (Blueprint $table) {
            $table->id();
            $table->string('building');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration'); //added start_time + end_time
            $table->text('message')->nullable();
            $table->integer('max_scholars');
            $table->integer('current_scholars')->default(0);
            $table->boolean('is_locked')->default(false);
            $table->enum('duty_status', ['Pending', 'Active', 'Completed', 'Cancelled'])->default('Pending');
            $table->boolean('is_completed')->default(false);
            $table->foreignId('prof_id')->constrained('users')->cascadeOnDelete(); // Foreign key to users (professors)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duties');
    }
}
