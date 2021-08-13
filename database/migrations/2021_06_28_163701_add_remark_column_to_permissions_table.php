<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class AddRemarkColumnToPermissionsTable
 */
class AddRemarkColumnToPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTableName(), function (Blueprint $table) {
            $table->string('remark', 50)->nullable()->after('guard_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->getTableName(), function (Blueprint $table) {
            $table->dropColumn('remark');
        });
    }

    private function getTableName()
    {
        return config('permission.table_names.permissions');
    }
}
