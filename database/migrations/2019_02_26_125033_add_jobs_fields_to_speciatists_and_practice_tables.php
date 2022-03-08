<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddJobsFieldsToSpeciatistsAndPracticeTables
 */
class AddJobsFieldsToSpeciatistsAndPracticeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('specialists', function (Blueprint $table) {
            $table->integer('jobs_total')->default(0);
            $table->float('hours_total')->default(0);
            $table->integer('reviews_total')->default(0);
            $table->float('average_stars')->default(0);
            $table->integer('reviews_to_practice_total')->default(0);
            $table->float('average_stars_to_practice')->default(0);
        });

        Schema::table('practices', function (Blueprint $table) {
            $table->integer('hires_total')->default(0);
            $table->float('average_stars')->default(0);
            $table->integer('reviews_to_provider_total')->default(0);
            $table->float('average_stars_to_provider')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('specialists', function (Blueprint $table) {
            $table->dropColumn('jobs_total');
            $table->dropColumn('hours_total');
            $table->dropColumn('reviews_total');
            $table->dropColumn('average_stars');
            $table->dropColumn('reviews_to_practice_total');
            $table->dropColumn('average_stars_to_practice');
        });
        Schema::table('practices', function (Blueprint $table) {
            $table->dropColumn('hires_total');
            $table->dropColumn('average_stars');
            $table->dropColumn('reviews_to_provider_total');
            $table->dropColumn('average_stars_to_provider');
        });
    }
}
