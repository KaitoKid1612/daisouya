<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDriverTaskPlanAllowDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_task_plan_allow_drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_task_plan_id')->comment('稼働依頼プラン')->constrained();
            $table->foreignId('driver_plan_id')->comment('ドライバープラン')->constrained();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            $table->unique(['driver_task_plan_id', 'driver_plan_id'],'driver_task_plan_id_driver_plan_id_unique');
        });

        DB::statement("ALTER TABLE driver_task_plan_allow_drivers COMMENT 'その稼働依頼プランで対応可能なドライバープラン'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_task_plan_allow_drivers');
    }
}
