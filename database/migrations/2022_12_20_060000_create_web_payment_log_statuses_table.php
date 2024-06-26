<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


class CreateWebPaymentLogStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_payment_log_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('ステータス名');
            $table->string('label')->comment('ラベル');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
        DB::statement("ALTER TABLE web_payment_log_statuses COMMENT '支払いログステータス'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('web_payment_log_statuses');
    }
}
