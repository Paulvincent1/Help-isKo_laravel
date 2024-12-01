<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRenewalFormsTable extends Migration
{
    public function up()
    {
        Schema::create('renewal_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); 
            $table->string('student_number');         
            $table->integer('attended_events');        
            $table->integer('shared_posts');          
            $table->string('registration_fee_picture'); 
            $table->string('disbursement_method');   
            $table->integer('duty_hours');            
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending'); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('renewal_forms');
    }
}
