<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddCheckrFieldsToSpecialistTable
 */
class AddCheckrFieldsToSpecialistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('specialists', function (Blueprint $table) {
            $table->string('checkr_status', 50)->default('not checked');
            $table->string('checkr_candidate_id', 255)->nullable();
            $table->string('checkr_report_id', 255)->nullable();
            $table->text('checkr_error_response')->nullable();
            $table->text('checkr_success_response')->nullable();
            $table->integer('checkr_attempts')->default(0);
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
            $table->dropColumn('checkr_status');
            $table->dropColumn('checkr_candidate_id');
            $table->dropColumn('checkr_report_id');
            $table->dropColumn('checkr_error_response');
            $table->dropColumn('checkr_success_response');
            $table->dropColumn('checkr_attempts');
        });
    }
}
