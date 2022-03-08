<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class ChangeForeignRestrictions
 */
class ChangeForeignRestrictions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('specialists', function (Blueprint $table) {
            $table->dropForeign(['industry_id']);
            $table->dropForeign(['position_id']);

            $table->foreign('industry_id')->references('id')->on('industries')->onDelete('SET NULL');
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('SET NULL');
        });

        Schema::table('practices', function (Blueprint $table) {
            $table->dropForeign(['industry_id']);

            $table->foreign('industry_id')->references('id')->on('industries')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
