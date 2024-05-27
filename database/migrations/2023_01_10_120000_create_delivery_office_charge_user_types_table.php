<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * 請求に関するユーザータイプ
 * 一般ユーザー、無料ユーザーなど
 */
class CreateDeliveryOfficeChargeUserTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_office_charge_user_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('請求に関するユーザの種類名');
            $table->string('summary')->comment('概要');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
        DB::statement("ALTER TABLE delivery_office_charge_user_types COMMENT '請求に関するユーザの種類'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_office_charge_user_types');
    }
}
