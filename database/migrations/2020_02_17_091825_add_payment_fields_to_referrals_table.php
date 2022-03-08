<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddPaymentFieldsToReferralsTable
 */
class AddPaymentFieldsToReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invites', function (Blueprint $table) {
            $table->float('bonus_value')->nullable();
            $table->string('status')->nullable();
            $table->string('charge_id')->nullable();
            $table->string('payment_system')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invites', function (Blueprint $table) {
            $table->dropColumn('bonus_value');
            $table->dropColumn('status');
            $table->dropColumn('charge_id');
            $table->dropColumn('payment_system');
        });
    }
}
