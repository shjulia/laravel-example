<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddCancellationFieldsToShiftsTable
 */
class AddCancellationFieldsToShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->string('cancellation_charge_id')->nullable();
            $table->float('cancellation_fee')->nullable();
            $table->text('cancellation_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn('cancellation_charge_id');
            $table->dropColumn('cancellation_fee');
            $table->dropColumn('cancellation_reason');
        });
    }
}
