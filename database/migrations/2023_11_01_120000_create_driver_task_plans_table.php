<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDriverTaskPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_task_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('プラン名');
            $table->string('label')->comment('ラベル');
            $table->integer('system_price')->nullable()->comment('システム利用料金');
            $table->integer('system_price_percent')->nullable()->comment('システム利用料金(運賃の%)');
            $table->integer('busy_system_price')->nullable()->comment('システム料金(繁忙期)');
            $table->integer('busy_system_price_percent')->nullable()->comment('システム料金(繁忙期,運賃の%)');
            $table->integer('busy_system_price_percent_over')->nullable()->comment('システム料金(繁忙期,運賃の%,既定運賃以上の場合)');
            $table->integer('emergency_price')->comment('緊急依頼料金');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });

        DB::statement("ALTER TABLE driver_task_plans COMMENT '稼働依頼プラン'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_task_plans');
    }
}
