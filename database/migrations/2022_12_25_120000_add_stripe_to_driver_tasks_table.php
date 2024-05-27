<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * stripe支払い用のカラムを追加
 */
class AddStripeToDriverTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_tasks', function (Blueprint $table) {
            $table->dateTime('request_date', $precision = 0)->nullable()->comment('申込日')->after('task_date');
            
            $table->after('task_addr4', function ($table) {
                $table->integer('basic_price')->default(0)->comment('基本料金');
                $table->integer('option_soon_price')->default(0)->comment('直前依頼オプション料金');
                $table->integer('discount')->default(0)->comment('値引き額');
                $table->integer('tax')->default(0)->comment('消費税');
                $table->float('tax_rate', 4, 2)->default(0.00)->comment('税率');
                $table->integer('refund_amount')->default(0)->comment('返金した金額');
                $table->float('payment_fee_rate', 4, 2)->default(0.00)->comment('決済手数料率');
                $table->string('stripe_payment_method_id')->default('')->comment('stripe支払い方法ID');
                $table->string('stripe_payment_intent_id')->default('')->comment('stripe支払いインテントID');
                $table->string('stripe_payment_refund_id')->default('')->comment('stripe返金ID');
                $table->foreignId('driver_task_payment_status_id')->nullable()->comment('支払いステータス')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreignId('driver_task_refund_status_id')->nullable()->comment('返金ステータス')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_tasks', function (Blueprint $table) {
            $table->dropColumn('basic_price');
            $table->dropColumn('option_soon_price');
            $table->dropColumn('discount');
            $table->dropColumn('tax');
            $table->dropColumn('tax_rate');
            $table->dropColumn('payment_fee_rate');
            $table->dropColumn('stripe_payment_method_id');
            $table->dropColumn('stripe_payment_intent_id');
            $table->dropColumn('driver_task_payment_status_id');
            $table->dropColumn('driver_task_refund_status_id');
        });
    }
}
