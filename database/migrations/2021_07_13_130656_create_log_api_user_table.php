<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateLogApiUserTable
 */
class CreateLogApiUserTable extends Migration
{
    private $schema;
    private $tableName = 'log_api_user';

    public function __construct()
    {
        $this->schema = Schema::connection('log_mariadb');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ($this->schema->hasTable($this->tableName)) {
            return;
        }

        $this->schema->create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('operator_id');
            $table->string('action', 30);
            $table->date('date');
            $table->dateTime('datetime');
            $table->boolean('success')->default(false);
            $table->char('code', 8)->nullable();
            $table->string('request_url')->nullable();
            $table->mediumText('request_params')->nullable();
//            $table->mediumText('request_headers')->nullable();
            $table->mediumText('response')->nullable();
            $table->mediumText('exception')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->char('trace_code', 32)->nullable();
            $table->char('auth_token', 32)->nullable();
            $table->timestamps();

            $table->index(['action','date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema->dropIfExists($this->tableName);
    }
}
