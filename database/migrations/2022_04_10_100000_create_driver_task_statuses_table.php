<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDriverTaskStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_task_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名前');
            $table->string('label')->comment('ラベル');
            $table->text('explanation')->comment('ステータスの説明');
        });
        DB::statement("ALTER TABLE driver_task_statuses COMMENT '稼働依頼ステータス'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_task_statuses');
    }
}
