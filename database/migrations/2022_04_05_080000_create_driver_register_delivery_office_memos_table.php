<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDriverRegisterDeliveryOfficeMemosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_register_delivery_office_memos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->nullable()->comment('ドライバーid(外部キー)')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('delivery_company_id')->nullable()->comment('配送会社id(外部キー)')->constrained()->cascadeOnDelete()->cascadeOnUpdate()->name('driver_register_office_memos_delivery_company_id_foreign');
            $table->string('delivery_office_name')->comment('営業所名前');
            $table->string('post_code1')->comment('郵便番号1');
            $table->string('post_code2')->comment('郵便番号2');
            $table->foreignId('addr1_id')->comment('都道府県')->constrained('prefectures');
            $table->string('addr2')->comment('市区町村');
            $table->string('addr3')->comment('住所3町域丁目 番地 号');
            $table->string('addr4')->comment('住所4建物名 部屋番号');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });

        DB::statement("ALTER TABLE driver_register_delivery_office_memos COMMENT 'ドライバーの登録済み営業所(メモ)'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_register_delivery_office_memos');
    }
}
