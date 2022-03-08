<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddPaymentStatusFieldToProviderChargesTable
 */
class AddPaymentStatusFieldToProviderChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('provider_charges', function (Blueprint $table) {
            $table->string('payment_status')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('provider_charges', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }
}
