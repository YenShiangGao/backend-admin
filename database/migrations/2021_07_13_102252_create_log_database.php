<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateLogDatabase
 */
class CreateLogDatabase extends Migration
{
    private string $databaseName = 'fubo_admin_log';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::createDatabase($this->databaseName);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropDatabaseIfExists($this->databaseName);
    }
}
