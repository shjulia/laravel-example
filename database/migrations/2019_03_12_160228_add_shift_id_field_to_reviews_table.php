<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddShiftIdFieldToReviewsTable
 */
class AddShiftIdFieldToReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedInteger('shift_id');

            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('CASCADE');
        });

        Schema::table('practice_reviews', function (Blueprint $table) {
            $table->boolean('score_friendly_team')->default(0)->change();
            $table->boolean('score_cool_office')->default(0)->change();
            $table->boolean('score_great_patient')->default(0)->change();
            $table->boolean('score_well_organized')->default(0)->change();
        });

        Schema::table('provider_reviews', function (Blueprint $table) {
            $table->boolean('score_patient_care')->default(0)->change();
            $table->boolean('score_friendly')->default(0)->change();
            $table->boolean('score_hard_worker')->default(0)->change();
            $table->boolean('score_works_well_with_team')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropColumn('shift_id');
        });
    }
}
