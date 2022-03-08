<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShiftInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_invites', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('shift_id');
            $table->unsignedInteger('provider_id');
            $table->string('status');
            $table->timestamps();

            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('cascade');
            $table->foreign('provider_id')->references('user_id')->on('specialists')->onDelete('cascade');
        });

        Schema::table('shifts', function (Blueprint $table) {
            $table->boolean('continue_search')->nullable();
            $table->timestamp('start_search_in')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_invites', function (Blueprint $table) {

            $table->dropForeign(['shift_id']);
            $table->dropForeign(['provider_id']);
        });
        Schema::dropIfExists('shift_invites');

        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn('continue_search');
            $table->dropColumn('start_search_in');
        });
    }
}
