<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceToWebConfigSystemsTable extends Migration
{
    /**
     * 料金設定のカラム追加
     */
    public function up()
    {
        Schema::table('web_config_systems', function (Blueprint $table) {
            $table->after('register_request_token_time_limit', function ($table) {
                $table->integer('default_price')->default(0)->comment('既定の基本料金');
                $table->integer('default_option_soon_price')->default(0)->comment('既定の直前依頼オプション料金');
                $table->float('default_tax_rate', 4, 2)->default(0.00)->comment('既定の税率');
                $table->float('default_stripe_payment_fee_rate', 4, 2)->default(0.00)->comment('既定のstripe決済手数料率');
                $table->integer('soon_price_time_limit_from')->default(0)->comment('直前依頼の時間の範囲。 稼働日0時0分の±何時から');
                $table->integer('soon_price_time_limit_to')->default(0)->comment('直前依頼の時間の範囲。 稼働日0時0分の±何時まで');
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
        Schema::table('web_config_systems', function (Blueprint $table) {
            $table->dropColumn('default_price');
            $table->dropColumn('default_option_soon_price');
            $table->dropColumn('default_tax_rate');
            $table->dropColumn('soon_price_time_limit_from');
            $table->dropColumn('soon_price_time_limit_to');
        });
    }
}
