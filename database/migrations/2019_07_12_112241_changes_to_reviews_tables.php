<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class ChangesToReviewsTables
 */
class ChangesToReviewsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('practice_reviews', function (Blueprint $table) {
            $table->dropColumn('score_friendly_team');
            $table->dropColumn('score_cool_office');
            $table->dropColumn('score_great_patient');
            $table->dropColumn('score_well_organized');
        });
        Schema::table('provider_reviews', function (Blueprint $table) {
            $table->dropColumn('score_patient_care');
            $table->dropColumn('score_friendly');
            $table->dropColumn('score_hard_worker');
            $table->dropColumn('score_works_well_with_team');
        });

        Schema::create('scores', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255);
            $table->string('for_type', 20);
            $table->boolean('active')->default(1);
        });

        Schema::create('practice_reviews_scores', function (Blueprint $table) {
            $table->unsignedInteger('practice_review_id');
            $table->unsignedInteger('score_id');
            $table->primary(['practice_review_id', 'score_id']);
            $table->foreign('practice_review_id')->references('review_id')->on('practice_reviews')->onDelete('CASCADE');
            $table->foreign('score_id')->references('id')->on('scores')->onDelete('CASCADE');
        });

        Schema::create('provider_reviews_scores', function (Blueprint $table) {
            $table->unsignedInteger('provider_review_id');
            $table->unsignedInteger('score_id');
            $table->primary(['provider_review_id', 'score_id']);
            $table->foreign('provider_review_id')->references('review_id')->on('provider_reviews')->onDelete('CASCADE');
            $table->foreign('score_id')->references('id')->on('scores')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('practice_reviews', function (Blueprint $table) {
            $table->double('score_friendly_team');
            $table->double('score_cool_office');
            $table->double('score_great_patient');
            $table->double('score_well_organized');
        });

        Schema::table('provider_reviews', function (Blueprint $table) {
            $table->double('score_patient_care');
            $table->double('score_friendly');
            $table->double('score_hard_worker');
            $table->double('score_works_well_with_team');
        });
        Schema::table('practice_reviews_scores', function (Blueprint $table) {
            $table->dropForeign(['practice_review_id']);
            $table->dropForeign(['score_id']);
        });
        Schema::table('provider_reviews_scores', function (Blueprint $table) {
            $table->dropForeign(['provider_review_id']);
            $table->dropForeign(['score_id']);
        });
        Schema::dropIfExists('practice_reviews_scores');
        Schema::dropIfExists('provider_reviews_scores');
        Schema::dropIfExists('scores');
    }
}
