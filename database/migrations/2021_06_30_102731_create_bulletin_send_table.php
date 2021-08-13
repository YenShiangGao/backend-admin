<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateBulletinSendTable
 */
class CreateBulletinSendTable extends Migration
{
    private $table = 'bulletin_send';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('type_id');
            $table->integer('bulletin_id')->index();
            $table->dateTime('bulletin_at');
            $table->string('platform_code', 10);
            $table->tinyInteger('status')->default(0); // 0:未發送 1:發送成功 2:發送失敗
            $table->timestamps();

            $table->index(['bulletin_at', 'platform_code']);
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
