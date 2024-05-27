<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDriverRegisterDeliveryOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_register_delivery_offices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->nullable()->comment('ドライバーid(外部キー)')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('delivery_office_id')->nullable()->comment('営業所id(外部キー)')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->primary([ 'driver_id', 'delivery_office_id'], 'register_offices_primary')->comment('複合主キー');
            $table->unique(['driver_id', 'delivery_office_id'],'register_offices_driver_id_delivery_office_id_unique');
        });
        DB::statement("ALTER TABLE driver_register_delivery_offices COMMENT 'ドライバーの登録済み営業所'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_register_delivery_offices');
    }
}
