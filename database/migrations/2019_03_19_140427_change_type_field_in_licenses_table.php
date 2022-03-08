<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTypeFieldInLicensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->unsignedInteger('type')->change();
        });

        Schema::table('licenses', function (Blueprint $table) {
            $table->foreign('type')
                ->references('id')->on('license_types')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropForeign(['type']);
            $table->dropIndex('licenses_type_foreign');
        });
        Schema::table('licenses', function (Blueprint $table) {
            $table->string('type', 50)->change();
        });
    }
}
