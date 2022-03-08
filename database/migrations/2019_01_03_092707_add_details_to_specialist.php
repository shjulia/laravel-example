<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddDetailsToSpecialist
 */
class AddDetailsToSpecialist extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('specialists', function (Blueprint $table) {
            $table->string('photo', 255)->nullable();
        });

        Schema::create('specialities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 50);
            $table->integer('industry_id')->unsigned();

            $table->foreign('industry_id')->references('id')->on('industries')->onDelete('CASCADE');
        });

        Schema::create('specialist_speciality', function (Blueprint $table) {
            $table->unsignedInteger('specialist_id');
            $table->unsignedInteger('speciality_id');

            $table->primary(['specialist_id', 'speciality_id']);
            $table->foreign('specialist_id')->references('user_id')->on('specialists')->onDelete('CASCADE');
            $table->foreign('speciality_id')->references('id')->on('specialities')->onDelete('CASCADE');
        });

        Schema::create('provider_availabilities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('day');
            $table->double('min_time');
            $table->double('max_time');
            $table->unsignedInteger('specialist_id');

            $table->foreign('specialist_id')->references('user_id')->on('specialists')->onDelete('CASCADE');
        });

        Schema::create('holidays', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
        });

        DB::table('holidays')->insert([
            ['title' => 'New Years Day (January 1)'],
            ['title' => 'Memorial Day (last Monday in May)'],
            ['title' => 'Independence Day (July 4th)'],
            ['title' => 'Labor Day (first Monday in September)'],
            ['title' => 'Thanksgiving Day (fourth Thursday in November)'],
            ['title' => 'Christmas Day (December 25)']
        ]);

        Schema::create('provider_holidays_availabilities', function (Blueprint $table) {
            $table->unsignedInteger('specialist_id');
            $table->unsignedInteger('holiday_id');

            $table->primary(['specialist_id', 'holiday_id'], 'prov_hol_av_spec_id_hol_id');
            $table->foreign('specialist_id')->references('user_id')->on('specialists')->onDelete('CASCADE');
            $table->foreign('holiday_id')->references('id')->on('holidays')->onDelete('CASCADE');
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
            $table->dropColumn('photo');
        });
        Schema::table('provider_availabilities', function (Blueprint $table) {
            $table->dropForeign(['specialist_id']);
        });
        Schema::table('specialist_speciality', function (Blueprint $table) {
            $table->dropForeign(['specialist_id']);
            $table->dropForeign(['speciality_id']);
        });
        Schema::dropIfExists('specialist_speciality');
        Schema::dropIfExists('specialities');
        Schema::dropIfExists('provider_availabilities');
        Schema::table('provider_holidays_availabilities', function (Blueprint $table) {
            $table->dropForeign(['specialist_id']);
            $table->dropForeign(['holiday_id']);
        });
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('provider_holidays_availabilities');
    }
}
