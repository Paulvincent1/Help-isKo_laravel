<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRenewalFormsTable extends Migration
{
    public function up()
    {
        Schema::table('renewal_forms', function (Blueprint $table) {
            $table->dropColumn('disbursement_method');
            $table->string('shared_posts')->change();
            $table->string('orf_url')->nullable()->after('registration_fee_picture');
        });
    }

    public function down()
    {
        Schema::table('renewal_forms', function (Blueprint $table) {
            $table->string('disbursement_method')->after('registration_fee_picture');
            $table->integer('shared_posts')->change();
            $table->dropColumn('orf_url');
        });
    }
}
