<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateBulletinTable
 */
class CreateBulletinTable extends Migration
{
    private $table = 'bulletin';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->integer('type_id');
            $table->string('subject', 100)->comment('主題');
            $table->text('content')->comment('內容');
            $table->tinyInteger('status')->default(1); // 0:disable 1:enable
            $table->integer('operator_id');
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
