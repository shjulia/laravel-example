<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecialistTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specialist_task', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('task_id');

            $table->foreign('user_id')
                ->references('user_id')
                ->on('specialists')
                ->onDelete('CASCADE');

            $table->foreign('task_id')
                ->references('id')
                ->on('tasks')
                ->onDelete('CASCADE');
        });
    }

    /**regions
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('specialist_task');
    }
}
