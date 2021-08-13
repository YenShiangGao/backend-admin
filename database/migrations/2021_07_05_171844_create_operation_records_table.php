<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperationRecordsTable extends Migration
{
    private $table = 'operation_records';
    private $partitionColumn = 'record_date';
    private $db;

    public function __construct()
    {
        $this->db = DB::connection();
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->runCreateQuery();
        $this->runAutoIncrementsQuery();
        $this->runPartitionQuery();
    }

    private function runCreateQuery()
    {
        if (Schema::hasTable($this->table)) {
            return;
        }

        Schema::create($this->table, function (Blueprint $table) {
            $table->bigInteger('id');
            $table->date($this->partitionColumn);
            $table->dateTime('record_time');

            $table->string('operator_model', 30);
            $table->bigInteger('operator_id');

            $table->string('category', 30)->comment('類別');
            $table->string('project', 50)->default('')->comment('項目');
            $table->string('subproject', 50)->default('')->comment('子項目');;

            $table->tinyInteger('action'); // 1:create / 2:update / 3:delete
            $table->string('model', 30);
            $table->bigInteger('model_id');
            $table->text('original');
            $table->text('changes')->nullable();
            $table->ipAddress('ip')->nullable();

            $table->timestamps();

            // pk
            $table->primary(['id', $this->partitionColumn]);

            $table->index([$this->partitionColumn, 'operator_id']);
            $table->index(['model', 'model_id']);
        });
    }

    private function runAutoIncrementsQuery()
    {
        Schema::table($this->table, function (Blueprint $table) {
            // auto increments
            $table->bigInteger('id', true)->change();
        });
    }

    private function runPartitionQuery()
    {
        $query = "ALTER TABLE {$this->table} PARTITION BY RANGE (month(`{$this->partitionColumn}`))
                    (PARTITION `m01` VALUES LESS THAN (2) ENGINE = InnoDB,
                     PARTITION `m02` VALUES LESS THAN (3) ENGINE = InnoDB,
                     PARTITION `m03` VALUES LESS THAN (4) ENGINE = InnoDB,
                     PARTITION `m04` VALUES LESS THAN (5) ENGINE = InnoDB,
                     PARTITION `m05` VALUES LESS THAN (6) ENGINE = InnoDB,
                     PARTITION `m06` VALUES LESS THAN (7) ENGINE = InnoDB,
                     PARTITION `m07` VALUES LESS THAN (8) ENGINE = InnoDB,
                     PARTITION `m08` VALUES LESS THAN (9) ENGINE = InnoDB,
                     PARTITION `m09` VALUES LESS THAN (10) ENGINE = InnoDB,
                     PARTITION `m10` VALUES LESS THAN (11) ENGINE = InnoDB,
                     PARTITION `m11` VALUES LESS THAN (12) ENGINE = InnoDB,
                     PARTITION `m12` VALUES LESS THAN (13) ENGINE = InnoDB,
                     PARTITION `m13` VALUES LESS THAN MAXVALUE ENGINE = InnoDB)";

        $this->db->statement($query);
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
