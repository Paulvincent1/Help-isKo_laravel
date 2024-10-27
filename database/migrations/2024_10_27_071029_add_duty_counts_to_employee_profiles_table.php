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
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->unsignedInteger('confirmed_duty')->default(0)->after('user_id');
            $table->unsignedInteger('active_duty')->default(0)->after('confirmed_duty');
            $table->unsignedInteger('posted_duty')->default(0)->after('active_duty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->dropColumn(['confirmed_duty', 'active_duty', 'posted_duty']);
        });
    }
};
