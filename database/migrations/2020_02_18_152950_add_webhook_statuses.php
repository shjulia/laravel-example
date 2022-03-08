<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddWebhookStatuses
 */
class AddWebhookStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('provider_bonuses', function (Blueprint $table) {
            $table->string('payment_status')->nullable()->after('status');
        });
        Schema::table('invites', function (Blueprint $table) {
            $table->string('payment_status')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('provider_bonuses', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
        Schema::table('invites', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }
}
