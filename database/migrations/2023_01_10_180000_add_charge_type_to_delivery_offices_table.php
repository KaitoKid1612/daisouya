<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChargeTypeToDeliveryOfficesTable extends Migration
{
    /**
     * 請求に関するユーザタイプ 追加
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_offices', function (Blueprint $table) {
            $table->after('remember_token', function ($table) {
                $table->unsignedBigInteger('charge_user_type_id')->nullable()->comment('請求に関するユーザタイプ');
                $table->foreign('charge_user_type_id')->references('id')->on('delivery_office_charge_user_types')->cascadeOnUpdate();
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
        Schema::table('delivery_offices', function (Blueprint $table) {
            $table->dropColumn('charge_user_type_id');
        });
    }
}
