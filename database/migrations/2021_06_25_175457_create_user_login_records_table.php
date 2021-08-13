<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateUserLoginRecordsTable
 */
class CreateUserLoginRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_login_records', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->ipAddress('ip');
            $table->string('agent');
            $table->string('device', 20)->nullable(); // pc mobile app
            $table->string('platform', 20)->nullable();
            $table->string('browser', 30)->nullable();
            $table->string('browser_ver', 30)->nullable();
            $table->string('mobile', 30)->nullable();
            $table->string('url', 50)->nullable();
            $table->dateTime('login_at');
            $table->string('auth_token', 32);

            $table->timestamps();

            $table->index(['login_at', 'user_id', 'ip']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_login_records');
    }
}
