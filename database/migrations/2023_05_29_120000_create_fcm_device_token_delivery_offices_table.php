<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateFcmDeviceTokenDeliveryOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fcm_device_token_delivery_offices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_office_id')->nullable()->comment('依頼者ID(外部キー)')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('device_name')->comment('device name');
            $table->string('fcm_token');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            $table->unique(['device_name', 'fcm_token']);
        });
        DB::statement("ALTER TABLE fcm_device_token_delivery_offices COMMENT 'FCMデバイストークン'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fcm_device_token_delivery_offices');
    }
}
