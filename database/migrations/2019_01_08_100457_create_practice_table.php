<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreatePracticeTable
 */
class CreatePracticeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('practices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('industry_id')->nullable();
            $table->string('practice_name', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('city', 255)->nullable();
            $table->string('state', 20)->nullable();
            $table->string('zip', 10)->nullable();
            $table->string('url', 255)->nullable();
            $table->string('practice_phone', 100)->nullable();

            $table->string('policy_photo', 255)->nullable();
            $table->string('policy_type', 50)->nullable();
            $table->string('policy_number', 255)->nullable();
            $table->timestamp('policy_expiration_date')->nullable();
            $table->string('policy_provider', 255)->nullable();
            $table->boolean('no_policy')->nullable();

            $table->string('practice_photo', 255)->nullable();
            $table->text('culture')->nullable();
            $table->text('notes')->nullable();
            $table->string('on_site_contact', 255)->nullable();
            $table->text('park')->nullable();
            $table->text('door')->nullable();
            $table->text('dress_code')->nullable();
            $table->text('info')->nullable();

            $table->string('card_number', 255)->nullable();
            $table->string('card_date', 255)->nullable();
            $table->string('card_csv', 255)->nullable();

            $table->foreign('industry_id')->references('id')->on('industries')->onDelete('CASCADE');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('practice_id')->nullable();

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
            $table->dropForeign(['practice_id']);
            $table->dropColumn('practice_id');
        });

        Schema::table('practices', function (Blueprint $table) {
            $table->dropForeign(['industry_id']);
        });
        Schema::drop('practices');
    }
}
