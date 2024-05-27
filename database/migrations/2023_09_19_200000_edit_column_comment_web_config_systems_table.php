<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * カラムコメント 変更
 */
class EditColumnCommentWebConfigSystemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('web_config_systems', function (Blueprint $table) {
            $table->integer('default_price')->comment('既定のシステム利用料金')->change();
            $table->integer('default_option_soon_price')->comment('既定の緊急依頼料金')->change();
            $table->integer('soon_price_time_limit_from')->comment('緊急依頼の時間の範囲。 稼働日0時0分の±何時から')->change();
            $table->integer('soon_price_time_limit_to')->comment('緊急依頼の時間の範囲。 稼働日0時0分の±何時まで')->change();
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
            $table->integer('default_price')->comment('既定の基本料金')->change();
            $table->integer('default_option_soon_price')->comment('既定の直前依頼オプション料金')->change();
            $table->integer('soon_price_time_limit_from')->comment('直前依頼の時間の範囲。 稼働日0時0分の±何時から')->change();
            $table->integer('soon_price_time_limit_to')->comment('直前依頼の時間の範囲。 稼働日0時0分の±何時まで')->change();
        });
    }
}
