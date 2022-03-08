<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class ChangePracticeRelations
 */
class ChangePracticeRelations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['practice_id']);
            $table->dropColumn('practice_id');
        });

        Schema::create('user_practice', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('practice_id');
            $table->string('practice_role', 50);
            $table->boolean('is_creator')->default(0);

            $table->primary(['user_id', 'practice_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->foreign('practice_id')->references('id')->on('practices')->onDelete('CASCADE');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('practice_id')->nullable();

            $table->foreign('practice_id')->references('id')->on('practices')->onDelete('CASCADE');
        });
        Schema::table('user_practice', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['practice_id']);
        });
        Schema::dropIfExists('user_practice');
    }
}
