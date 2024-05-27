<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDeliveryPickupAddrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_pickup_addrs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_office_id')->nullable()->comment('営業所ID')->constrained('delivery_offices');
            $table->string('delivery_company_name')->comment('配送会社名');
            $table->string('delivery_office_name')->comment('営業所名');
            $table->string('email')->comment('メールアドレス');
            $table->string('tel')->comment('電話番号');
            $table->string('post_code1')->comment('郵便番号1');
            $table->string('post_code2')->comment('郵便番号2');
            $table->foreignId('addr1_id')->comment('都道府県')->constrained('prefectures');
            $table->string('addr2')->comment('市区町村');
            $table->string('addr3')->comment('丁目 番地 号');
            $table->string('addr4')->comment('建物名 部屋番号');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
        DB::statement("ALTER TABLE delivery_pickup_addrs COMMENT '登録用集荷先住所'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_pickup_addrs');
    }
}
