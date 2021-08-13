<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateRoleClosureTable
 */
class CreateRoleClosureTable extends Migration
{
    private string $table = 'role_closure';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->bigInteger('role_id')->unsigned()->index();
            $table->bigInteger('ancestor_id')->unsigned()->index();
            $table->integer('depth', false, true);
            $table->tinyInteger('status', false, true)->default(1); // 0:disable 1:enable
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->unique(['role_id', 'ancestor_id', 'status']);
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
