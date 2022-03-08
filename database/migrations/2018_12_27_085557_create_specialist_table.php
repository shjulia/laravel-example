<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateSpecialistTable
 */
class CreateSpecialistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specialists', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('industry_id')->nullable();
            $table->unsignedInteger('position_id')->nullable();

            $table->string('driver_photo', 255)->nullable();
            $table->string('driver_address', 255)->nullable();
            $table->string('driver_city', 255)->nullable();
            $table->string('driver_state', 20)->nullable();
            $table->string('driver_zip', 10)->nullable();
            $table->string('driver_first_name', 255)->nullable();
            $table->string('driver_last_name', 255)->nullable();
            $table->string('driver_middle_name', 255)->nullable();
            $table->timestamp('driver_expiration_date')->nullable();
            $table->string('driver_gender', 2)->nullable();

            $table->primary('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->foreign('industry_id')->references('id')->on('industries')->onDelete('CASCADE');
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('CASCADE');
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
            $table->dropForeign(['user_id']);
            $table->dropForeign(['industry_id']);
            $table->dropForeign(['position_id']);
        });
        Schema::dropIfExists('specialists');
    }
}
