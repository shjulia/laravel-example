<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewsTabls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('date');
            $table->double('score');
            $table->text('text');
            $table->text('answer')->nullable();
            $table->timestamps();
        });

        Schema::create('practice_reviews', function (Blueprint $table) {
            $table->integer('review_id')->unsigned();
            $table->integer('from_provider_id')->unsigned();
            $table->integer('practice_id')->unsigned();

            $table->double('score_friendly_team');
            $table->double('score_cool_office');
            $table->double('score_great_patient');
            $table->double('score_well_organized');

            $table->primary('review_id');
            $table->foreign('review_id')->references('id')->on('reviews')->onDelete('CASCADE');
            $table->foreign('from_provider_id')->references('user_id')->on('specialists')->onDelete('CASCADE');
            $table->foreign('practice_id')->references('id')->on('practices')->onDelete('CASCADE');
        });

        Schema::create('provider_reviews', function (Blueprint $table) {
            $table->integer('review_id')->unsigned();
            $table->integer('from_practice_id')->unsigned();
            $table->integer('provider_id')->unsigned();

            $table->double('score_patient_care');
            $table->double('score_friendly');
            $table->double('score_hard_worker');
            $table->double('score_works_well_with_team');

            $table->primary('review_id');
            $table->foreign('review_id')->references('id')->on('reviews')->onDelete('CASCADE');
            $table->foreign('provider_id')->references('user_id')->on('specialists')->onDelete('CASCADE');
            $table->foreign('from_practice_id')->references('id')->on('practices')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('provider_reviews', function (Blueprint $table) {
            $table->dropForeign(['review_id']);
            $table->dropForeign(['provider_id']);
            $table->dropForeign(['from_practice_id']);
        });
        Schema::dropIfExists('provider_reviews');
        Schema::table('practice_reviews', function (Blueprint $table) {
            $table->dropForeign(['review_id']);
            $table->dropForeign(['from_provider_id']);
            $table->dropForeign(['practice_id']);
        });
        Schema::dropIfExists('practice_reviews');
        Schema::dropIfExists('reviews');
    }
}
