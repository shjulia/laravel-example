<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddMultiDaysFieldToShiftsTable
 */
class AddMultiDaysFieldToShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->integer('multi_days')->default(0)->after('status');
            $table->unsignedInteger('parent_shift_id')->nullable()->after('id');

            $table->foreign('parent_shift_id')->references('id')->on('shifts')->onDelete('cascade');
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
            $table->dropColumn('multi_days');
            $table->dropForeign(['parent_shift_id']);
            $table->dropColumn('parent_shift_id');
        });
    }
}
