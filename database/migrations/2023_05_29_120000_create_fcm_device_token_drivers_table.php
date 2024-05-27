<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateFcmDeviceTokenDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fcm_device_token_drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->nullable()->comment('ドライバーID(外部キー)')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('device_name');
            $table->string('fcm_token');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            $table->unique(['device_name', 'fcm_token']);
        });
        DB::statement("ALTER TABLE fcm_device_token_drivers COMMENT 'FCMデバイストークン'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fcm_device_token_drivers');
    }
}
