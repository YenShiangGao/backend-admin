<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreatePlatformTable
 */
class CreatePlatformTable extends Migration
{
    private $table = 'platform';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('name', 30);
            $table->string('code', 10)->unique();
            $table->string('currency')->nullable()->comment('開放幣別');
            $table->tinyInteger('agent_site_status')->default(0)->comment('管端狀態'); // 0:disable 1:enable
            $table->tinyInteger('member_site_status')->default(0)->comment('客端狀態'); // 0:disable 1:enable
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
