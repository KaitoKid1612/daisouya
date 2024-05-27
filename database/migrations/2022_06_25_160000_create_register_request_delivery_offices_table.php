<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateRegisterRequestDeliveryOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('register_request_delivery_offices', function (Blueprint $table) {
            $table->id();
            $table->string('token')->nullable()->unique()->comment('登録トークン');
            $table->foreignId('register_request_status_id')->comment('登録申請ステータスID(外部キー)')->constrained()->cascadeOnDelete()->cascadeOnUpdate()->name('regi_request_delivery_offices_register_request_status_id_foreign');
            $table->string('name')->comment('営業所名');
            $table->string('manager_name_sei')->comment('担当者 姓');
            $table->string('manager_name_mei')->comment('担当者 名');
            $table->string('manager_name_sei_kana')->comment('担当者 姓 読み仮名(カナ)');
            $table->string('manager_name_mei_kana')->comment('担当者 名 読み仮名(カナ)');
            $table->string('email')->comment('メールアドレス');
            $table->foreignId('delivery_company_id')->nullable()->comment('配送会社ID(外部キー)')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('delivery_company_name')->comment('配送会社名');
            $table->foreignId('delivery_office_type_id')->comment('依頼者種類')->constrained()->cascadeOnDelete()->cascadeOnUpdate()->name('register_request_delivery_office_type_id_foreign');
            $table->string('post_code1')->comment('郵便番号1');
            $table->string('post_code2')->comment('郵便番号2');
            $table->foreignId('addr1_id')->comment('都道府県')->constrained('prefectures');
            $table->string('addr2')->comment('市区町村');
            $table->string('addr3')->comment('丁目 番地 号');
            $table->string('addr4')->comment('建物名 部屋番号');
            $table->string('manager_tel')->comment('担当者電話番号');
            $table->text('message')->comment('メッセージ、備考');
            $table->dateTime('time_limit_at')->nullable()->comment('登録の期限');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });

        DB::statement("ALTER TABLE register_request_delivery_offices COMMENT '登録申請 配送営業所'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('register_request_delivery_offices');
    }
}
