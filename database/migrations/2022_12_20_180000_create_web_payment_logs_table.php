<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


class CreateWebPaymentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_payment_logs', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date')->comment('支払い日');
            $table->integer('amount_money')->comment('金額');
            $table->bigInteger('driver_task_id')->nullable()->comment('稼働ID');
            $table->foreignId('web_payment_log_status_id')->comment('支払いログステータス(外部キー)')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('web_payment_reason_id')->comment('支払いログ事由(外部キー)')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('message')->comment('メッセージ');
            $table->unsignedBigInteger('pay_user_id')->nullable()->comment('支払いユーザID');
            $table->unsignedBigInteger('pay_user_type_id')->comment('支払いユーザの種類 外部キー');
            $table->foreign('pay_user_type_id')->references('id')->on('user_types');
            $table->bigInteger('receive_user_id')->nullable()->comment('受け取りユーザID')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedBigInteger('receive_user_type_id')->comment('受け取りユーザの種類 外部キー');
            $table->foreign('receive_user_type_id')->references('id')->on('user_types')->cascadeOnDelete()->cascadeOnUpdate();

            $table->dateTime('created_at');
            $table->dateTime('updated_at');

        });
        DB::statement("ALTER TABLE web_payment_logs COMMENT '支払いログ'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('web_payment_logs');
    }
}
