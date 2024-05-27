<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDriverTaskRefundStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_task_refund_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('ステータス名');
            $table->string('label')->comment('ラベル');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
        DB::statement("ALTER TABLE driver_task_refund_statuses COMMENT '稼働依頼返金ステータス'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_task_refund_statuses');
    }
}
